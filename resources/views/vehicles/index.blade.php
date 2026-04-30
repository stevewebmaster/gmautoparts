@extends('layouts.kars')

@section('title', 'Now Dismantling')
@section('meta_description', 'Vehicles we are currently dismantling. Browse parts available from each vehicle.')

@section('content')
    <div class="breadcumb-wrapper style-2" data-bg-src="/images/page-headers/Dismantling-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Now Dismantling</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Now Dismantling</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="reservation-area style-2 home-4-style">
        <div class="container th-container">
            <div class="reservation-wrapper">
                <div class="reservation-form">
                    <form action="{{ route('vehicles.index') }}" method="GET">
                        <div class="row">
                            <div class="col-12">
                                <div class="select-group-wrapper">
                                    <div class="form-group">
                                        <input type="text" name="make" class="form-control" placeholder="Make" value="{{ request('make') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="model" class="form-control" placeholder="Model" value="{{ request('model') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="stock_number" class="form-control" placeholder="Stock Number" value="{{ request('stock_number') }}">
                                    </div>
                                    <div class="advance-btn-wrapper">
                                        <button class="th-btn w-100" type="submit">
                                            Search Vehicles <i class="fas fa-arrow-up-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
                            <form action="{{ route('vehicles.index') }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="make" value="{{ request('make') }}">
                                <input type="hidden" name="model" value="{{ request('model') }}">
                                <input type="hidden" name="year" value="{{ request('year') }}">
                                <input type="hidden" name="stock_number" value="{{ request('stock_number') }}">
                                <div class="form-group mb-0">
                                    <select name="sort" class="form-select nice-select" onchange="this.form.submit()">
                                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Sort By Latest</option>
                                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort By Oldest</option>
                                        <option value="year_desc" {{ request('sort') === 'year_desc' ? 'selected' : '' }}>Year: Newest First</option>
                                        <option value="year_asc" {{ request('sort') === 'year_asc' ? 'selected' : '' }}>Year: Oldest First</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-30 justify-content-center">
                @forelse($vehicles as $vehicle)
                    <div class="col-xl-6 col-lg-12">
                        <div class="feature-list-1 list">
                            <div class="box-icon">
                                @if(is_array($vehicle->images) && count($vehicle->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}" alt="{{ $vehicle->display_name }}">
                                @else
                                    <img src="/kars/img/featured/featured-1-1.jpg" alt="{{ $vehicle->display_name }}">
                                @endif
                            </div>
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
                    <div class="col-12 text-center">
                        <p class="text-body-secondary mb-0">No vehicles found for your current filters.</p>
                    </div>
                @endforelse
            </div>

            @if($vehicles->hasPages())
                <div class="row">
                    <div class="col-lg-12 mt-5 text-center">
                        <div class="th-pagination th-pagination mt-xl-3 mb-0">
                            {{ $vehicles->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
