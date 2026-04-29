<div>
    <div class="reservation-area style-2 home-4-style mb-40">
        <div class="container th-container p-0">
            <div class="reservation-wrapper">
                <div class="reservation-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="select-group-wrapper">
                                <div class="form-group">
                                    <select wire:model.live="make" class="form-select nice-select">
                                        <option value="">All Makes</option>
                                        @foreach($makes as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select wire:model.live="model" class="form-select nice-select">
                                        <option value="">All Models</option>
                                        @foreach($models as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select wire:model.live="year" class="form-select nice-select">
                                        <option value="">All Years</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select wire:model.live="category" class="form-select nice-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="advance-btn-wrapper">
                                    <button wire:click="clearFilters" type="button" class="th-btn w-100">
                                        Reset <i class="fas fa-arrow-up-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="advance-search-wrapper stye-2 home-4-style">
                        <div class="form-group">
                            <select wire:model.live="subcategory" class="form-select nice-select">
                                <option value="">All Subcategories</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text"
                                   wire:model.live.debounce.300ms="keyword"
                                   class="form-control"
                                   placeholder="Search parts, stock number, description...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <div class="icon-item active">
                        <i class="fa-regular fa-grid"></i>
                    </div>
                    <div class="icon-item">
                        <i class="fa-solid fa-list"></i>
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
                        <div class="actions">
                            <a href="{{ route('parts.show', $part->slug) }}" class="icon-btn"><i class="fa-regular fa-tag"></i></a>
                            <a href="{{ route('parts.show', $part->slug) }}" class="icon-btn"><i class="far fa-heart"></i></a>
                        </div>
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
                        <ul class="car-feature">
                            <li>
                                <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-1.svg" alt="icon"></div>
                                {{ $part->year ?: 'N/A' }}
                            </li>
                            <li class="divider"></li>
                            <li>
                                <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-2.svg" alt="icon"></div>
                                {{ $part->make ?: 'Unknown Make' }}
                            </li>
                            <li class="divider"></li>
                            <li>
                                <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-3.svg" alt="icon"></div>
                                {{ $part->model ?: 'Unknown Model' }}
                            </li>
                        </ul>
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
