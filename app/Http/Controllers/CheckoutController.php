<?php

namespace App\Http\Controllers;

use App\Enums\PartStatus;
use App\Mail\OrderConfirmation;
use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\Part;
use App\Services\Cart;
use App\Services\StripeCheckout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected Cart $cart,
        protected StripeCheckout $stripe,
    ) {
    }

    public function show(): View|RedirectResponse
    {
        if ($this->cart->purchasable()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('checkout.show', [
            'items' => $this->cart->purchasable(),
            'subtotal' => $this->cart->subtotal(),
            'band' => $this->cart->dominantBand(),
            'regions' => config('shipping.regions'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'fulfilment' => ['required', Rule::in(['pickup', 'delivery'])],
            'address_line1' => 'required_if:fulfilment,delivery|nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'suburb' => 'nullable|string|max:255',
            'city' => 'required_if:fulfilment,delivery|nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'region' => ['required_if:fulfilment,delivery', 'nullable', Rule::in(array_keys(config('shipping.regions')))],
            'is_rural' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (! $this->stripe->configured()) {
            Log::error('Checkout attempted with no Stripe secret configured');

            return back()->withErrors([
                'checkout' => 'Online payment is not available right now. Please call us on 07 849 8814 and we will take your order.',
            ])->withInput();
        }

        $isRural = (bool) ($validated['is_rural'] ?? false);
        $region = $validated['fulfilment'] === 'delivery' ? $validated['region'] : null;

        try {
            $order = DB::transaction(function () use ($validated, $region, $isRural) {
                $ids = $this->cart->purchasable()->pluck('id')->all();

                if (empty($ids)) {
                    return null;
                }

                // Lock the rows and re-check: someone else may have bought one
                // of these between the cart page and this submit.
                $parts = Part::whereIn('id', $ids)->lockForUpdate()->get();

                foreach ($parts as $part) {
                    if (! $part->isPurchasable()) {
                        return null;
                    }
                }

                $subtotal = round((float) $parts->sum(fn (Part $p) => (float) $p->price), 2);
                $shipping = $this->cart->shipping($validated['fulfilment'], $region, $isRural);

                $order = Order::create([
                    'reference' => Order::generateReference(),
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'fulfilment' => $validated['fulfilment'],
                    'address_line1' => $validated['address_line1'] ?? null,
                    'address_line2' => $validated['address_line2'] ?? null,
                    'suburb' => $validated['suburb'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'postcode' => $validated['postcode'] ?? null,
                    'region' => $region,
                    'is_rural' => $isRural,
                    'notes' => $validated['notes'] ?? null,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => round($subtotal + $shipping, 2),
                    'expires_at' => now()->addMinutes(Order::PENDING_HOLD_MINUTES),
                ]);

                foreach ($parts as $part) {
                    $order->items()->create([
                        'part_id' => $part->id,
                        'title' => $part->title,
                        'price' => $part->price,
                        'quantity' => 1,
                        'shipping_band' => $part->shipping_band,
                    ]);

                    // Hold it while they pay; released if they never do.
                    $part->update(['status' => PartStatus::OnHold]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            Log::error('Order creation failed', ['exception' => $e]);

            return back()->withErrors([
                'checkout' => 'Sorry, something went wrong creating your order. Please try again or call us on 07 849 8814.',
            ])->withInput();
        }

        if (! $order) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Sorry, one of the parts in your cart has just been sold. Please review your cart and try again.',
            ]);
        }

        // Stripe session creation is outside the transaction: a network call
        // should not hold row locks, and a failure here must leave a releasable
        // order behind rather than rolling back into an inconsistent state.
        try {
            $session = $this->stripe->createSession($order->load('items'));

            $order->update(['stripe_session_id' => $session->id]);

            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            Log::error('Stripe session creation failed', [
                'order' => $order->reference,
                'exception' => $e,
            ]);

            $order->release();

            return redirect()->route('cart.index')->withErrors([
                'cart' => 'We could not reach the payment provider. Nothing has been charged. Please try again shortly, or call us on 07 849 8814.',
            ]);
        }
    }

    /**
     * Stripe sends the customer back here. This confirms payment so the page is
     * truthful immediately, but the webhook is the authority — a customer who
     * closes the tab still gets a paid order.
     */
    public function success(Request $request, string $reference): View
    {
        $order = Order::where('reference', $reference)->with('items')->firstOrFail();

        if (! $order->isPaid() && $request->filled('session_id')) {
            try {
                $session = $this->stripe->retrieveSession($request->get('session_id'));

                if ($session->payment_status === 'paid' && $session->metadata->order_reference === $order->reference) {
                    if ($order->markPaid($session->payment_intent)) {
                        $this->sendEmails($order->fresh('items'));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Could not confirm Stripe session on success page', [
                    'order' => $order->reference,
                    'exception' => $e,
                ]);
            }
        }

        if ($order->isPaid()) {
            $this->cart->clear();
        }

        return view('checkout.success', ['order' => $order->fresh('items')]);
    }

    public function cancel(string $reference): RedirectResponse
    {
        $order = Order::where('reference', $reference)->first();

        // Put the parts straight back on sale rather than waiting for the
        // sweep — the customer told us they are not paying.
        $order?->release();

        return redirect()->route('cart.index')->with('success', 'Your order was cancelled and nothing has been charged. The parts are back in your cart.');
    }

    public function webhook(Request $request): Response
    {
        try {
            $event = $this->stripe->verifyWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature'),
            );
        } catch (\Throwable $e) {
            Log::warning('Rejected Stripe webhook', ['message' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $reference = $session->metadata->order_reference ?? null;
            $order = $reference ? Order::where('reference', $reference)->with('items')->first() : null;

            if ($order && $session->payment_status === 'paid') {
                if ($order->markPaid($session->payment_intent)) {
                    $this->sendEmails($order->fresh('items'));
                }
            }
        }

        if (in_array($event->type, ['checkout.session.expired'], true)) {
            $reference = $event->data->object->metadata->order_reference ?? null;
            $order = $reference ? Order::where('reference', $reference)->with('items')->first() : null;
            $order?->release();
        }

        return response('ok', 200);
    }

    protected function sendEmails(Order $order): void
    {
        $adminEmail = config('mail.from.address', env('ADMIN_EMAIL', 'admin@example.com'));

        try {
            Mail::to($order->email)->queue(new OrderConfirmation($order));
            Mail::to($adminEmail)->queue(new OrderReceived($order));
        } catch (\Throwable $e) {
            Log::error('Order emails could not be queued', [
                'order' => $order->reference,
                'exception' => $e,
            ]);
        }
    }
}
