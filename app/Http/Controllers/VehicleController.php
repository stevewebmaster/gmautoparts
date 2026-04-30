<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehiclesQuery = Vehicle::query()
            ->where('is_visible', true)
            ->withCount('parts');

        if ($request->filled('make')) {
            $vehiclesQuery->where('make', 'like', '%' . $request->string('make')->trim() . '%');
        }

        if ($request->filled('model')) {
            $vehiclesQuery->where('model', 'like', '%' . $request->string('model')->trim() . '%');
        }

        if ($request->filled('year')) {
            $vehiclesQuery->where('year', (int) $request->input('year'));
        }

        if ($request->filled('stock_number')) {
            $vehiclesQuery->where('stock_number', 'like', '%' . $request->string('stock_number')->trim() . '%');
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $vehiclesQuery->oldest();
        } elseif ($sort === 'year_desc') {
            $vehiclesQuery->orderByDesc('year')->latest();
        } elseif ($sort === 'year_asc') {
            $vehiclesQuery->orderBy('year')->latest();
        } else {
            $vehiclesQuery->latest();
        }

        $vehicles = $vehiclesQuery->paginate(12);

        return view('vehicles.index', ['vehicles' => $vehicles]);
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
