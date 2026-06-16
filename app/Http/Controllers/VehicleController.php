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

        $makes = Vehicle::where('is_visible', true)
            ->whereNotNull('make')->where('make', '!=', '')
            ->distinct()->orderBy('make')->pluck('make');

        $modelsQuery = Vehicle::where('is_visible', true)
            ->whereNotNull('model')->where('model', '!=', '');
        if ($request->filled('make')) {
            $modelsQuery->where('make', $request->string('make')->trim());
        }
        $models = $modelsQuery->distinct()->orderBy('model')->pluck('model');

        $yearsQuery = Vehicle::where('is_visible', true)
            ->whereNotNull('year');
        if ($request->filled('make')) {
            $yearsQuery->where('make', $request->string('make')->trim());
        }
        if ($request->filled('model')) {
            $yearsQuery->where('model', $request->string('model')->trim());
        }
        $years = $yearsQuery->distinct()->orderByDesc('year')->pluck('year');

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'makes' => $makes,
            'models' => $models,
            'years' => $years,
        ]);
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
