<?php

namespace App\Http\Controllers;

use App\Enums\PartStatus;
use App\Enums\OrderStatus;
use App\Enums\ReservationStatus;
use App\Enums\ShippingBand;
use App\Models\Order;
use App\Models\Part;
use App\Models\Reservation;
use App\Models\PartCategory;
use App\Models\PartSubcategory;
use App\Models\Vehicle;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MiniappController extends Controller
{
    private const WRONG_PINS_PER_ADDRESS = 10;

    private const WRONG_PINS_SITE_WIDE = 20;

    private const SITE_WIDE_KEY = 'miniapp-login:site-wide';

    private const DEVICE_COOKIE = 'miniapp_device';

    public function login(): View|RedirectResponse
    {
        if (session('miniapp_authenticated')) {
            return redirect()->route('app.dashboard');
        }
        return view('miniapp.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $pin = trim((string) $request->pin);
        $expected = trim((string) preg_replace('/[\r\n]+/', '', config('miniapp.pin') ?? ''));

        if ($expected === '') {
            return back()->withErrors([
                'pin' => 'Mini-app PIN is not set on the server. Add MINIAPP_PIN to the .env file in the app directory (the same folder as artisan), then run: php artisan config:clear && php artisan config:cache',
            ])->withInput();
        }

        // A PIN is only safe if guesses are slow, so wrong ones are limited two
        // ways. Only wrong guesses count: staff logging in often are never held up.
        //
        // - Per address: 10 an hour.
        // - Site-wide: 20 an hour from devices that have never logged in. Without
        //   it, a bot spread over 1,000 addresses gets 10,000 guesses an hour.
        //   A phone that has logged in before carries a cookie that skips this
        //   limit, so a bot that trips it cannot lock the yard out.
        $addressKey = 'miniapp-login:' . $request->ip();
        $knownDevice = $this->isKnownDevice($request, $expected);

        if (RateLimiter::tooManyAttempts($addressKey, self::WRONG_PINS_PER_ADDRESS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($addressKey) / 60);

            return back()->withErrors(['pin' => "Too many wrong PINs. Try again in {$minutes} minutes."]);
        }

        if (! $knownDevice && RateLimiter::tooManyAttempts(self::SITE_WIDE_KEY, self::WRONG_PINS_SITE_WIDE)) {
            $minutes = (int) ceil(RateLimiter::availableIn(self::SITE_WIDE_KEY) / 60);

            return back()->withErrors(['pin' => "Too many wrong PINs have been tried. A phone that has not logged in before can try again in {$minutes} minutes."]);
        }

        if (! hash_equals($expected, $pin)) {
            RateLimiter::hit($addressKey, 3600);

            if (RateLimiter::attempts($addressKey) === self::WRONG_PINS_PER_ADDRESS) {
                Log::warning('Mini-app login locked for one address after repeated wrong PINs', ['ip' => $request->ip()]);
            }

            if (! $knownDevice) {
                RateLimiter::hit(self::SITE_WIDE_KEY, 3600);

                if (RateLimiter::attempts(self::SITE_WIDE_KEY) === self::WRONG_PINS_SITE_WIDE) {
                    Log::warning('Mini-app login paused for new devices: too many wrong PINs site-wide', ['last_ip' => $request->ip()]);
                }
            }

            // No withInput(): it would keep the wrong PIN in the session.
            return back()->withErrors(['pin' => 'Invalid PIN.']);
        }

        // Only this address's count is cleared. The site-wide one stays, so a
        // correct login does not hand a bot a fresh allowance.
        RateLimiter::clear($addressKey);
        $request->session()->regenerate();
        session(['miniapp_authenticated' => true]);

        return redirect()->route('app.dashboard')->withCookie(cookie(
            self::DEVICE_COOKIE,
            $this->deviceToken($expected),
            60 * 24 * 365,
            '/app',
        ));
    }

    /**
     * The value a phone's cookie holds once it has logged in. It is tied to the
     * PIN, so changing the PIN makes every phone a new device again. Laravel
     * encrypts cookies with the app key, so it cannot be forged.
     */
    private function deviceToken(string $pin): string
    {
        return hash_hmac('sha256', 'miniapp-device|' . $pin, (string) config('app.key'));
    }

    private function isKnownDevice(Request $request, string $pin): bool
    {
        return hash_equals($this->deviceToken($pin), (string) $request->cookie(self::DEVICE_COOKIE));
    }

    public function logout(Request $request)
    {
        session()->forget('miniapp_authenticated');
        return redirect()->route('app.login');
    }

    public function dashboard(): View
    {
        return view('miniapp.dashboard', [
            'holdingCount' => Reservation::holding()->count(),
            'toPackCount' => Order::where('status', OrderStatus::Paid)->count(),
        ]);
    }

    public function subcategories(PartCategory $category)
    {
        $subs = PartSubcategory::where('part_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return response()->json($subs);
    }

    public function createPart(): View
    {
        $categories = PartCategory::where('is_active', true)->orderBy('sort_order')->get();
        $vehicles = Vehicle::where('is_visible', true)->orderByRaw('year DESC')->get();

        return view('miniapp.parts.create', [
            'categories' => $categories,
            'vehicles' => $vehicles,
        ]);
    }

    public function storePart(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'part_category_id' => 'required|exists:part_categories,id',
            'part_subcategory_id' => 'nullable|exists:part_subcategories,id',
            'description' => 'nullable|string|max:5000',
            'price' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string|max:100',
            'shipping_band' => ['nullable', Rule::in(ShippingBand::values())],
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'stock_number' => 'nullable|string|max:100',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:20480',
        ]);

        $slug = Str::slug($validated['title']);
        $base = $slug;
        $i = 0;
        while (Part::where('slug', $slug)->exists()) {
            $i++;
            $slug = $base . '-' . $i;
        }
        $validated['slug'] = $slug;

        $paths = [];
        foreach ($request->file('images') ?? [] as $file) {
            if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) {
                continue;
            }
            $stored = ImageOptimizer::optimizeAndStore($file, 'parts');
            if ($stored) {
                $paths[] = $stored;
            }
        }
        if (empty($paths)) {
            return back()->withErrors(['images' => 'Could not save images. Please try again.'])->withInput();
        }
        $validated['images'] = $paths;
        $validated['is_visible'] = true;
        $validated['is_featured'] = false;

        try {
            Part::create($validated);
        } catch (\Throwable $e) {
            Log::error('Mini-app storePart failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withErrors([
                'images' => 'Could not save the part. Try setting MINIAPP_OPTIMIZE_UPLOADS=false in server .env, then php artisan config:cache. Also confirm php artisan storage:link and writable storage/. See storage/logs/laravel.log.',
            ])->withInput();
        }

        return redirect()->route('app.dashboard')->with('success', 'Part added. It will appear on the website.');
    }

    /**
     * Stock list for marking parts sold from the yard. Search covers the things
     * you can read off a part or a shelf label: title, stock number, make, model.
     */
    public function stock(Request $request): View
    {
        $search = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $unsized = $request->boolean('unsized');

        $parts = Part::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    foreach (['title', 'stock_number', 'make', 'model'] as $column) {
                        $inner->orWhere($column, 'like', '%' . $search . '%');
                    }
                });
            })
            ->when(in_array($status, PartStatus::values(), true), fn ($query) => $query->where('status', $status))
            // Parts on sale that cannot be bought online until measured.
            ->when($unsized, fn ($query) => $query->where('status', PartStatus::Available)->whereNull('shipping_band'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('miniapp.parts.stock', [
            'parts' => $parts,
            'search' => $search,
            'status' => $status,
            'unsized' => $unsized,
        ]);
    }

    /** Measure an existing part and set the satchel or box it goes in. */
    public function editFreight(Part $part): View
    {
        // Back to the same search or filter afterwards, so working down a
        // list of unmeasured parts does not lose your place.
        $previous = url()->previous();
        $returnTo = str_starts_with($previous, route('app.stock')) ? $previous : route('app.stock');

        return view('miniapp.parts.freight', ['part' => $part, 'returnTo' => $returnTo]);
    }

    public function updateFreight(Request $request, Part $part): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_band' => ['nullable', Rule::in(ShippingBand::values())],
            'return_to' => 'nullable|string',
        ]);

        $part->update(['shipping_band' => $validated['shipping_band'] ?? null]);

        $size = $part->shipping_band?->shortLabel() ?? 'not measured';
        $returnTo = $validated['return_to'] ?? '';

        return redirect()->to(str_starts_with($returnTo, route('app.stock')) ? $returnTo : route('app.stock'))
            ->with('success', "\"{$part->title}\" freight set to {$size}.");
    }

    public function updatePartStatus(Request $request, Part $part): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(PartStatus::values())],
        ]);

        $part->update(['status' => $validated['status']]);

        $label = PartStatus::from($validated['status'])->label();

        return back()->with('success', "\"{$part->title}\" is now marked {$label}.");
    }

    /**
     * Paid orders waiting to be picked, packed and sent. Pending orders are
     * shown too so unpaid checkouts are visible, but they need no action —
     * they release themselves.
     */
    public function orders(Request $request): View
    {
        $status = (string) $request->get('status', OrderStatus::Paid->value);

        $orders = Order::query()
            ->with('items')
            ->when(in_array($status, OrderStatus::values(), true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('miniapp.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'toPackCount' => Order::where('status', OrderStatus::Paid)->count(),
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                OrderStatus::Dispatched->value,
                OrderStatus::Collected->value,
            ])],
        ]);

        $status = OrderStatus::from($validated['status']);

        $order->update([
            'status' => $status,
            'dispatched_at' => $status === OrderStatus::Dispatched ? now() : $order->dispatched_at,
        ]);

        return back()->with('success', "{$order->reference} marked {$status->label()}.");
    }

    /**
     * Reservations waiting to be collected, oldest first so the ones closest to
     * expiring surface at the top.
     */
    public function reservations(Request $request): View
    {
        $status = (string) $request->get('status', ReservationStatus::Reserved->value);

        $reservations = Reservation::query()
            ->with('part')
            ->when(in_array($status, ReservationStatus::values(), true), fn ($q) => $q->where('status', $status))
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('miniapp.reservations.index', [
            'reservations' => $reservations,
            'status' => $status,
            'holdingCount' => Reservation::holding()->count(),
        ]);
    }

    public function updateReservationStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                ReservationStatus::Collected->value,
                ReservationStatus::Cancelled->value,
            ])],
        ]);

        // Route through the model so the part's status follows: collected sells
        // it, cancelled puts it back on the market.
        if ($validated['status'] === ReservationStatus::Collected->value) {
            $reservation->markCollected();
            $message = "{$reservation->reference} marked collected. The part is now Sold.";
        } else {
            $reservation->cancel();
            $message = "{$reservation->reference} cancelled. The part is back on sale.";
        }

        return back()->with('success', $message);
    }

    public function createVehicle(): View
    {
        return view('miniapp.vehicles.create');
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:20',
            'engine' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'stock_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:20480',
        ]);

        $paths = [];
        foreach ($request->file('images') ?? [] as $file) {
            if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) {
                continue;
            }
            $stored = ImageOptimizer::optimizeAndStore($file, 'vehicles');
            if ($stored) {
                $paths[] = $stored;
            }
        }
        if (empty($paths)) {
            return back()->withErrors(['images' => 'Could not save images. Please try again.'])->withInput();
        }
        $validated['images'] = $paths;
        $validated['is_visible'] = true;

        try {
            Vehicle::create($validated);
        } catch (\Throwable $e) {
            Log::error('Mini-app storeVehicle failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withErrors([
                'images' => 'Could not save the vehicle. Try setting MINIAPP_OPTIMIZE_UPLOADS=false in server .env, then php artisan config:cache. Also confirm php artisan storage:link and writable storage/. See storage/logs/laravel.log.',
            ])->withInput();
        }

        return redirect()->route('app.dashboard')->with('success', 'Vehicle added. It will appear in Now Dismantling.');
    }
}
