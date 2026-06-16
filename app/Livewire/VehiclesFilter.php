<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehiclesFilter extends Component
{
    use WithPagination;

    public string $make = '';
    public string $model = '';
    public string $year = '';
    public string $stockNumber = '';
    public string $sort = 'latest';

    protected $queryString = [
        'make' => ['except' => ''],
        'model' => ['except' => ''],
        'year' => ['except' => ''],
        'stockNumber' => ['except' => '', 'as' => 'stock_number'],
        'sort' => ['except' => 'latest'],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['make', 'model', 'year', 'stockNumber', 'sort'])) {
            $this->resetPage();
        }
    }

    public function getMakesProperty(): array
    {
        return Vehicle::where('is_visible', true)
            ->whereNotNull('make')
            ->where('make', '!=', '')
            ->distinct()
            ->orderBy('make')
            ->pluck('make')
            ->toArray();
    }

    public function getModelsProperty(): array
    {
        $query = Vehicle::where('is_visible', true)
            ->whereNotNull('model')
            ->where('model', '!=', '');

        if ($this->make) {
            $query->where('make', $this->make);
        }

        return $query->distinct()->orderBy('model')->pluck('model')->toArray();
    }

    public function getYearsProperty(): array
    {
        $query = Vehicle::where('is_visible', true)->whereNotNull('year');

        if ($this->make) {
            $query->where('make', $this->make);
        }
        if ($this->model) {
            $query->where('model', $this->model);
        }

        return $query->distinct()->orderByDesc('year')->pluck('year')->toArray();
    }

    public function clearFilters(): void
    {
        $this->make = '';
        $this->model = '';
        $this->year = '';
        $this->stockNumber = '';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Vehicle::query()
            ->where('is_visible', true)
            ->withCount('parts');

        if ($this->make) {
            $query->where('make', 'like', '%' . $this->make . '%');
        }
        if ($this->model) {
            $query->where('model', 'like', '%' . $this->model . '%');
        }
        if ($this->year) {
            $query->where('year', $this->year);
        }
        if ($this->stockNumber) {
            $query->where('stock_number', 'like', '%' . $this->stockNumber . '%');
        }

        if ($this->sort === 'oldest') {
            $query->oldest();
        } elseif ($this->sort === 'year_desc') {
            $query->orderByDesc('year')->latest();
        } elseif ($this->sort === 'year_asc') {
            $query->orderBy('year')->latest();
        } else {
            $query->latest();
        }

        $vehicles = $query->paginate(12);

        return view('livewire.vehicles-filter', [
            'vehicles' => $vehicles,
            'makes' => $this->getMakesProperty(),
            'models' => $this->getModelsProperty(),
            'years' => $this->getYearsProperty(),
        ]);
    }
}
