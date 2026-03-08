<?php

namespace App\Livewire;

use App\Models\Part;
use App\Models\PartCategory;
use Livewire\Component;
use Livewire\WithPagination;

class PartsFilter extends Component
{
    use WithPagination;

    public string $make = '';
    public string $model = '';
    public string $year = '';
    public string $category = '';
    public string $subcategory = '';
    public string $keyword = '';

    protected $queryString = [
        'make' => ['except' => ''],
        'model' => ['except' => ''],
        'year' => ['except' => ''],
        'category' => ['except' => ''],
        'subcategory' => ['except' => ''],
        'keyword' => ['except' => ''],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['make', 'model', 'year', 'category', 'subcategory', 'keyword'])) {
            $this->resetPage();
        }
    }

    public function getMakesProperty(): array
    {
        return Part::where('is_visible', true)
            ->whereNotNull('make')
            ->where('make', '!=', '')
            ->distinct()
            ->orderBy('make')
            ->pluck('make')
            ->toArray();
    }

    public function getModelsProperty(): array
    {
        $q = Part::where('is_visible', true)->whereNotNull('model')->where('model', '!=', '');
        if ($this->make) {
            $q->where('make', $this->make);
        }
        return $q->distinct()->orderBy('model')->pluck('model')->toArray();
    }

    public function getYearsProperty(): array
    {
        $q = Part::where('is_visible', true)->whereNotNull('year')->where('year', '!=', '');
        if ($this->make) {
            $q->where('make', $this->make);
        }
        if ($this->model) {
            $q->where('model', $this->model);
        }
        return $q->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
    }

    public function getCategoriesProperty()
    {
        return PartCategory::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function getSubcategoriesProperty()
    {
        if (!$this->category) {
            return collect();
        }
        return \App\Models\PartSubcategory::where('part_category_id', $this->category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function clearFilters(): void
    {
        $this->make = '';
        $this->model = '';
        $this->year = '';
        $this->category = '';
        $this->subcategory = '';
        $this->keyword = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Part::query()
            ->where('is_visible', true)
            ->with(['category', 'subcategory', 'vehicle']);

        if ($this->make) {
            $query->where('make', 'like', '%' . $this->make . '%');
        }
        if ($this->model) {
            $query->where('model', 'like', '%' . $this->model . '%');
        }
        if ($this->year) {
            $query->where('year', 'like', '%' . $this->year . '%');
        }
        if ($this->category) {
            $query->where('part_category_id', $this->category);
        }
        if ($this->subcategory) {
            $query->where('part_subcategory_id', $this->subcategory);
        }
        if ($this->keyword !== '') {
            $keyword = $this->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('stock_number', 'like', '%' . $keyword . '%');
            });
        }

        $parts = $query->latest()->paginate(12);

        return view('livewire.parts-filter', [
            'parts' => $parts,
            'makes' => $this->getMakesProperty(),
            'models' => $this->getModelsProperty(),
            'years' => $this->getYearsProperty(),
            'categories' => $this->getCategoriesProperty(),
            'subcategories' => $this->getSubcategoriesProperty(),
        ]);
    }
}
