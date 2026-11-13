<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('vehicles.index');
    }

    public function show(Request $request, Vehicle $vehicle): View|RedirectResponse
    {
        if (!$vehicle->is_visible) {
            abort(404);
        }

        // One address per vehicle: an old number-only link, or one from before
        // the vehicle was renamed, moves permanently to the current one.
        if ($request->route()->originalParameter('vehicle') !== $vehicle->url_key) {
            $query = $request->getQueryString();

            return redirect()->to(route('vehicles.show', $vehicle) . ($query ? '?' . $query : ''), 301);
        }

        $vehicle->load('parts.category');
        $parts = $vehicle->parts()->where('is_visible', true)->paginate(24);
        return view('vehicles.show', ['vehicle' => $vehicle, 'parts' => $parts]);
    }
}
