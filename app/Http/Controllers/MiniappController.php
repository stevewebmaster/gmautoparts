<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\PartCategory;
use App\Models\PartSubcategory;
use App\Models\Vehicle;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MiniappController extends Controller
{
    public function login(): View
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

        if ($pin !== $expected) {
            return back()->withErrors(['pin' => 'Invalid PIN.'])->withInput();
        }

        session(['miniapp_authenticated' => true]);

        return redirect()->route('app.dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('miniapp_authenticated');
        return redirect()->route('app.login');
    }

    public function dashboard(): View
    {
        return view('miniapp.dashboard');
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
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'stock_number' => 'nullable|string|max:100',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:5120',
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
        foreach ($request->file('images') as $file) {
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

        Part::create($validated);

        return redirect()->route('app.dashboard')->with('success', 'Part added. It will appear on the website.');
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
            'images.*' => 'image|max:5120',
        ]);

        $paths = [];
        foreach ($request->file('images') as $file) {
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

        Vehicle::create($validated);

        return redirect()->route('app.dashboard')->with('success', 'Vehicle added. It will appear in Now Dismantling.');
    }
}
