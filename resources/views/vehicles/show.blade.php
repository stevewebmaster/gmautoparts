@extends('layouts.kars')

@section('title', $vehicle->display_name . ' - Now Dismantling')
@section('meta_description', 'Parts available from ' . $vehicle->display_name . '.')

@section('content')
    <div class="breadcumb-wrapper style-2 compact-header" data-bg-src="/images/page-headers/Dismantling-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $vehicle->display_name }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('vehicles.index') }}">Now Dismantling</a></li>
                    <li>{{ $vehicle->display_name }}</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="th-inventory-wrapper inventory-details space-extra-bottom" style="padding-top: 60px;">
        <div class="container">
            <div class="row gy-4 gx-40">
                <div class="col-xxl-8 col-lg-7">

                    {{-- Vehicle image --}}
                    @if(is_array($vehicle->images) && count($vehicle->images))
                        <div class="mb-4">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}"
                                 alt="{{ $vehicle->display_name }}"
                                 class="img-fluid w-100 rounded"
                                 style="max-height: 480px; object-fit: cover;">
                        </div>
                    @endif

                    {{-- Vehicle title + feature strip --}}
                    <div class="th-inventory inventory-single">
                        <div class="inventory-single-top">
                            <div class="media-body">
                                <h2 class="box-title">{{ $vehicle->display_name }}</h2>
                                @if($vehicle->stock_number)
                                    <p class="box-text"><span>Stock #:</span> {{ $vehicle->stock_number }}</p>
                                @endif
                            </div>
                            <ul class="car-feature">
                                @if($vehicle->year)
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-date-1-1.svg" alt="Year"></div>
                                        <p>{{ $vehicle->year }}</p>
                                    </li>
                                @endif
                                @if($vehicle->engine)
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-1.svg" alt="Engine"></div>
                                        <p>{{ $vehicle->engine }}</p>
                                    </li>
                                @endif
                                @if($vehicle->transmission)
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-2.svg" alt="Transmission"></div>
                                        <p>{{ $vehicle->transmission }}</p>
                                    </li>
                                @endif
                                <li>
                                    <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-3.svg" alt="Parts"></div>
                                    <p>{{ $parts->total() }} parts</p>
                                </li>
                            </ul>
                        </div>

                        @if($vehicle->notes)
                            <div class="single-inventory-content">
                                <h5 class="title">Notes</h5>
                                <p>{{ $vehicle->notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Vehicle overview --}}
                    <div class="vehicle-overview-wrap">
                        <h5 class="title">Vehicle Overview</h5>
                        <div class="vehicle-overview-body">
                            @if($vehicle->year)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-10.svg" alt="icon"></div>
                                        <p>Year</p>
                                    </div>
                                    <div class="right"><p>{{ $vehicle->year }}</p></div>
                                </div>
                            @endif
                            @if($vehicle->engine)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-6.svg" alt="icon"></div>
                                        <p>Engine</p>
                                    </div>
                                    <div class="right"><p>{{ $vehicle->engine }}</p></div>
                                </div>
                            @endif
                            @if($vehicle->transmission)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-2.svg" alt="icon"></div>
                                        <p>Transmission</p>
                                    </div>
                                    <div class="right"><p>{{ $vehicle->transmission }}</p></div>
                                </div>
                            @endif
                            @if($vehicle->stock_number)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-14.svg" alt="icon"></div>
                                        <p>Stock #</p>
                                    </div>
                                    <div class="right"><p>{{ $vehicle->stock_number }}</p></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Parts grid --}}
                    <div class="mt-50">
                        <h5 class="title">Parts from this vehicle</h5>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mt-2">
                            @forelse($parts as $part)
                                <div class="col">
                                    <a href="{{ route('parts.show', $part->slug) }}" class="feature-list-1 text-decoration-none">
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
                                                <h3 class="box-title">{{ $part->title }}</h3>
                                                <p class="box-text">{{ $part->category->name }}</p>
                                            </div>
                                            <div class="car-bottom">
                                                @if($part->price)
                                                    <h6 class="box-title">${{ number_format($part->price, 2) }}</h6>
                                                @endif
                                                <span class="th-btn sm style3">View Part <i class="fas fa-arrow-up-right"></i></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-body-secondary py-4">No parts listed yet for this vehicle.</p>
                                </div>
                            @endforelse
                        </div>

                        @if($parts->hasPages())
                            <div class="th-pagination mt-40 text-center">
                                {{ $parts->links() }}
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="col-xxl-4 col-lg-5">
                    <aside class="sidebar-area">
                        <div class="widget widget-style-smoke shadow-style">
                            <div class="inventory-owner-wrap">
                                <div class="inventory-owner-top">
                                    <div class="content">
                                        <h6 class="box-title">Enquire About Parts</h6>
                                        <p class="saving">{{ $vehicle->display_name }}</p>
                                    </div>
                                </div>
                                <div class="inventory-owner-body">
                                    <div class="info-box">
                                        <div class="info-box_icon"><i class="fa-solid fa-phone"></i></div>
                                        <div class="info-contnt">
                                            <p class="info-box_text">
                                                <a href="tel:+6478498814" class="info-box_link">07 849 8814</a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-box_icon"><i class="fa-solid fa-location-dot"></i></div>
                                        <div class="info-contnt">
                                            <p class="info-box_text">Hamilton, New Zealand</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="inventory-owner-bottom">
                                    <a href="{{ route('contact') }}" class="th-btn style-2 w-100">
                                        Send Enquiry <i class="fas fa-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('vehicles.index') }}" class="th-btn style3 dealer w-100">
                                        <i class="fas fa-arrow-left"></i> Back to Now Dismantling
                                    </a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
