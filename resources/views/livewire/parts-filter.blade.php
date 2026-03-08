<div class="parts-filter" wire:key="parts-filter-wrapper">
    <div class="filter-panel card">
        <div class="card-body">
            <h3 class="card-title h6 mb-3">Filter parts</h3>
            <form wire:submit.prevent="">
                <div class="mb-3">
                    <label for="filter-make" class="form-label">Make</label>
                    <select id="filter-make" wire:model.live="make" class="form-select">
                        <option value="">All makes</option>
                        @foreach($makes as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="filter-model" class="form-label">Model</label>
                    <select id="filter-model" wire:model.live="model" class="form-select">
                        <option value="">All models</option>
                        @foreach($models as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="filter-year" class="form-label">Year</label>
                    <select id="filter-year" wire:model.live="year" class="form-select">
                        <option value="">All years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="filter-category" class="form-label">Category</label>
                    <select id="filter-category" wire:model.live="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($subcategories->isNotEmpty())
                    <div class="mb-3">
                        <label for="filter-subcategory" class="form-label">Subcategory</label>
                        <select id="filter-subcategory" wire:model.live="subcategory" class="form-select">
                            <option value="">All subcategories</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="mb-3">
                    <label for="filter-keyword" class="form-label">Keyword</label>
                    <input type="text" id="filter-keyword" wire:model.live.debounce.300ms="keyword" class="form-control" placeholder="Search title, description...">
                </div>
                <button type="button" wire:click="clearFilters" class="btn btn-outline-secondary w-100">Clear filters</button>
            </form>
        </div>
    </div>
    <div class="filter-results">
        <p class="text-body-secondary small mb-2">{{ $parts->total() }} part(s) found</p>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3" wire:key="parts-grid-{{ $parts->currentPage() }}">
            @forelse($parts as $part)
                <a href="{{ route('parts.show', $part->slug) }}" class="part-card card h-100 text-decoration-none text-body">
                    <div class="part-card-image card-img-top">
                        @if(is_array($part->images) && count($part->images))
                            <img src="{{ Storage::url($part->images[0]) }}" alt="{{ $part->title }}" loading="lazy" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="part-card-placeholder">No image</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $part->title }}</h5>
                        <p class="card-text small text-body-secondary">{{ $part->category->name }} @if($part->vehicle) · {{ $part->vehicle->display_name }} @endif</p>
                        @if($part->price)<p class="part-card-price mb-0">${{ number_format($part->price, 2) }}</p>@endif
                    </div>
                </a>
            @empty
                <p class="text-body-secondary text-center py-4 col-12">No parts match your filters. Try adjusting the criteria.</p>
            @endforelse
        </div>
        <div class="pagination-wrap mt-3">
            {{ $parts->links() }}
        </div>
    </div>
</div>
