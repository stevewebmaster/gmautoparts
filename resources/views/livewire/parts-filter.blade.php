<div class="row gy-4">
{{-- Left Sidebar --}}
<div class="col-xxl-3 col-xl-3 col-lg-4" wire:key="parts-filter-sidebar">

    <aside class="sidebar-area">

        <div class="widget widget-style-smoke search-top">
            <div class="h5 box-title">Search Options</div>
        </div>

        <div class="widget widget-style-smoke search">

            {{-- Make --}}
            <div class="inventory-search-item">
                <div class="form-group">
                    <select wire:model.live="make" class="form-select">
                        <option value="">All Makes</option>
                        @foreach($makes as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Model --}}
            <div class="inventory-search-item">
                <div class="form-group">
                    <select wire:model.live="model" class="form-select">
                        <option value="">All Models</option>
                        @foreach($models as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Year --}}
            <div class="inventory-search-item">
                <div class="form-group">
                    <select wire:model.live="year" class="form-select">
                        <option value="">All Years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Category --}}
            <div class="inventory-search-item">
                <div class="form-group">
                    <select wire:model.live="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Subcategory (shown when category selected) --}}
            @if($subcategories->isNotEmpty())
                <div class="inventory-search-item">
                    <div class="form-group">
                        <select wire:model.live="subcategory" class="form-select">
                            <option value="">All Subcategories</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            {{-- Keyword --}}
            <div class="inventory-search-item">
                <div class="form-group" style="position:relative;">
                    <input type="text"
                           wire:model.live.debounce.300ms="keyword"
                           class="form-control"
                           placeholder="Search parts...">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#aaa;pointer-events:none;"></i>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="inventory-search-item">
                <button wire:click="clearFilters" type="button"
                        class="th-btn left-icon style3 w-100">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filters
                </button>
            </div>

        </div>
    </aside>
</div>

{{-- Results Column --}}
<div class="col-xxl-9 col-xl-9 col-lg-8" wire:key="parts-filter-results">

    {{-- Top filter bar --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="inventory-top-filer-wrap">
                <div class="left-content">
                    <p>
                        Showing {{ $parts->firstItem() ?? 0 }}–{{ $parts->lastItem() ?? 0 }}
                        of {{ $parts->total() }} part(s)
                    </p>
                </div>
                <div class="filter-search">
                    <div class="icon-item">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <div class="icon-item">
                        <i class="fa-regular fa-grid"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Parts Grid --}}
    <div class="row gy-30 justify-content-center" wire:key="parts-grid-{{ $parts->currentPage() }}">

        @forelse($parts as $part)
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-sm-6">
                <div class="feature-list-1">
                    <div class="box-icon">
                        @if(is_array($part->images) && count($part->images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}"
                                 alt="{{ $part->title }}" loading="lazy">
                        @else
                            <img src="/kars/img/featured/featured-1-1.jpg" alt="{{ $part->title }}">
                        @endif
                    </div>
                    <div class="car-content">
                        <div class="media-body">
                            <h3 class="box-title">
                                <a href="{{ route('parts.show', $part->slug) }}">{{ $part->title }}</a>
                            </h3>
                            <p class="box-text">
                                <span>Category:</span>
                                {{ $part->category->name }}
                                @if($part->vehicle)
                                    &nbsp;&middot;&nbsp;{{ $part->vehicle->display_name }}
                                @endif
                            </p>
                        </div>
                        <div class="car-bottom">
                            @if($part->price)
                                <h6 class="box-title">${{ number_format($part->price, 2) }}</h6>
                            @else
                                <h6 class="box-title">POA</h6>
                            @endif
                            <a class="th-btn sm style3" href="{{ route('parts.show', $part->slug) }}">
                                View Details <i class="fas fa-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-body-secondary">No parts match your filters. Try adjusting the criteria.</p>
                <button wire:click="clearFilters" type="button" class="th-btn style3 mt-2">
                    Reset Filters <i class="fas fa-arrow-up-right"></i>
                </button>
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if($parts->hasPages())
        <div class="th-pagination text-center mt-40">
            {{ $parts->links() }}
        </div>
    @endif

</div>
</div>
