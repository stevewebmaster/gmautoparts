<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('vehicles.index');
    }

    public function show(Vehicle $vehicle): View
    {
        if (!$vehicle->is_visible) {
            abort(404);
        }
        $vehicle->load('parts.category');
        $parts = $vehicle->parts()->where('is_visible', true)->paginate(24);
        return view('vehicles.show', ['vehicle' => $vehicle, 'parts' => $parts]);
    }
}
