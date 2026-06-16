<div>
    <div class="reservation-area style-2 home-4-style">
        <div class="container th-container">
            <div class="reservation-wrapper">
                <div class="reservation-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="select-group-wrapper">
                                <div class="form-group">
                                    <select wire:model.live="make" class="form-select">
                                        <option value="">All Makes</option>
                                        @foreach($makes as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select wire:model.live="model" class="form-select">
                                        <option value="">All Models</option>
                                        @foreach($models as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select wire:model.live="year" class="form-select">
                                        <option value="">All Years</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="stockNumber"
                                           class="form-control"
                                           placeholder="Stock Number">
                                </div>
                                <div class="advance-btn-wrapper">
                                    <button wire:click="clearFilters" type="button" class="th-btn w-100">
                                        Reset <i class="fas fa-arrow-up-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="feature-sec-1 space">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="inventory-top-filer-wrap">
                        <div class="left-content">
                            <p>Showing {{ $vehicles->firstItem() ?? 0 }}-{{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} vehicles</p>
                        </div>
                        <div class="filter-search">
                            <div class="form-group mb-0">
                                <select wire:model.live="sort" class="form-select">
                                    <option value="latest">Sort By Latest</option>
                                    <option value="oldest">Sort By Oldest</option>
                                    <option value="year_desc">Year: Newest First</option>
                                    <option value="year_asc">Year: Oldest First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-30 justify-content-center" wire:key="vehicles-grid-{{ $vehicles->currentPage() }}">
                @forelse($vehicles as $vehicle)
                    <div class="col-xl-6 col-lg-12">
                        <div class="feature-list-1 list">
                            <a class="box-icon" href="{{ route('vehicles.show', $vehicle) }}">
                                @if(is_array($vehicle->images) && count($vehicle->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}" alt="{{ $vehicle->display_name }}">
                                @else
                                    <img src="/kars/img/featured/featured-1-1.jpg" alt="{{ $vehicle->display_name }}">
                                @endif
                            </a>
                            <div class="car-content">
                                <div class="media-body">
                                    <h3 class="box-title">
                                        <a href="{{ route('vehicles.show', $vehicle) }}">{{ $vehicle->display_name }}</a>
                                    </h3>
                                    <p class="box-text">
                                        <span>Stock:</span> {{ $vehicle->stock_number ?: 'N/A' }}
                                    </p>
                                </div>

                                <ul class="car-feature">
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-1.svg" alt="Engine"></div>
                                        {{ $vehicle->engine ?: 'Engine N/A' }}
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-2.svg" alt="Transmission"></div>
                                        {{ $vehicle->transmission ?: 'Transmission N/A' }}
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-3.svg" alt="Available parts"></div>
                                        {{ $vehicle->parts_count }} parts
                                    </li>
                                </ul>

                                <div class="car-bottom">
                                    <h6 class="box-title">{{ $vehicle->year ?: 'Year N/A' }}</h6>
                                    <a class="th-btn sm style3" href="{{ route('vehicles.show', $vehicle) }}">
                                        View Parts <i class="fas fa-arrow-up-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-body-secondary mb-0">No vehicles match your filters. Try adjusting the criteria.</p>
                        <button wire:click="clearFilters" type="button" class="th-btn style3 mt-2">
                            Reset Filters <i class="fas fa-arrow-up-right"></i>
                        </button>
                    </div>
                @endforelse
            </div>

            @if($vehicles->hasPages())
                <div class="row">
                    <div class="col-lg-12 mt-5 text-center">
                        <div class="th-pagination th-pagination mt-xl-3 mb-0">
                            {{ $vehicles->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
