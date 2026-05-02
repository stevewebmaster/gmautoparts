@extends('layouts.kars')

@push('head_styles')
<style>
    /* Kars theme reserves ~550px for .reservation-right; full width with that column removed */
    #hero .reservation-wrapper .reservation-form {
        width: 100%;
        max-width: 100%;
    }

    .gm-brand-box {
        min-height: 170px;
        display: flex !important;
        flex-direction: column;
        align-items: stretch;
        justify-content: space-between;
        grid-template-rows: unset !important;
    }
    .gm-brand-box-main {
        padding: 12px 8px 4px;
    }
    .gm-brand-box-yard {
        font-size: 0.72rem;
        letter-spacing: 0.02em;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding-top: 6px;
        margin-top: 2px;
    }
    .gm-brand-box-yard:hover {
        color: var(--theme-color, #ef2323) !important;
    }
</style>
@endpush

@section('title', 'G&M Auto Parts')
@section('meta_description', 'G&M Auto Parts - quality used car parts in New Zealand. Browse parts and vehicles we are now dismantling.')

@section('content')
    @php
        $dbOk = true;
        try {
            $slides     = \App\Models\SliderSlide::where('is_active', true)->orderBy('sort_order')->get();
            $categories = \App\Models\PartCategory::where('is_active', true)->orderBy('sort_order')->get();
            $featuredParts  = \App\Models\Part::where('is_visible', true)->where('is_featured', true)->latest()->take(8)->get();
            $dismantling    = \App\Models\Vehicle::where('is_visible', true)->withCount('parts')->latest()->take(6)->get();
        } catch (\Throwable $e) {
            $dbOk = false;
            $slides = $categories = $featuredParts = $dismantling = collect();
        }

        // Hero slide images (public/images/slider-cars/) — paired with titles when no CMS slides
        $heroSlideImages = ['/images/slider-cars/Corvette-Slider.png', '/images/slider-cars/Commodore2.png'];
        $heroTitles = ['Find Your Perfect Part', 'Quality Parts at Fair Prices'];

        $manufacturerBrands = collect(config('manufacturers.popular', []))
            ->filter(function ($b) {
                return !empty($b['file'])
                    && file_exists(public_path('images/manufacturers/'.$b['file']));
            })
            ->values();
    @endphp

    {{-- ==============================
         Hero Area
    ============================== --}}
    <div class="th-hero-wrapper hero-1" id="hero">
        <div class="th-hero-bg" data-bg-src="/kars/img/bg/hero_bg_1_1.jpg"></div>

        <div class="swiper th-slider" id="heroSlidee1"
             data-slider-options='{"effect":"fade","autoplay":{"delay":5000},"loop":true}'>
            <div class="swiper-wrapper">

                @if($slides->isNotEmpty())
                    @foreach($slides as $i => $slide)
                        <div class="swiper-slide">
                            <div class="hero-inner hero-style1">
                                <div class="container th-container">
                                    <div class="row gy-50 gx-40 align-items-center">
                                        <div class="col-xxl-5 col-xl-6 col-lg-6">
                                            <div class="hero-1-content">
                                                <span class="sub-title" data-ani="slideinup" data-ani-delay="0.2s">
                                                    <span class="text-theme">KIWI</span> OWNED AND OPERATED
                                                </span>
                                                <h1 class="hero-title" data-ani="slideinup" data-ani-delay="0.4s">
                                                    {{ $slide->title ?: 'Find Your Perfect Part' }}
                                                </h1>
                                                <p class="hero-text" data-ani="slideinup" data-ani-delay="0.6s">
                                                    {{ $slide->subtitle ?: 'Quality used automotive parts from dismantled vehicles across New Zealand. Browse our catalogue to find what you need.' }}
                                                </p>
                                                <div class="btn-group" data-ani="slideinup" data-ani-delay="0.8s">
                                                    <a href="{{ route('parts.index') }}" class="th-btn style2">Browse Parts <i class="fas fa-arrow-up-right"></i></a>
                                                    <a href="{{ route('contact') }}" class="th-btn style3 text-white">Contact Us <i class="fas fa-arrow-up-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-7 col-xl-6 col-lg-6">
                                            <div class="hero-img text-end">
                                                <div class="img-main" data-ani="slideinright" data-ani-delay="0.8s">
                                                    <img src="{{ $heroSlideImages[$i % count($heroSlideImages)] }}"
                                                         alt="{{ $slide->title ?: 'Vehicle' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Fallback when no CMS slides --}}
                    @foreach($heroSlideImages as $i => $img)
                        <div class="swiper-slide">
                            <div class="hero-inner hero-style1">
                                <div class="container th-container">
                                    <div class="row gy-50 gx-40 align-items-center">
                                        <div class="col-xxl-5 col-xl-6 col-lg-6">
                                            <div class="hero-1-content">
                                                <span class="sub-title" data-ani="slideinup" data-ani-delay="0.2s">
                                                    <span class="text-theme">KIWI</span> OWNED AND OPERATED
                                                </span>
                                                <h1 class="hero-title" data-ani="slideinup" data-ani-delay="0.4s">
                                                    {{ $heroTitles[$i] }}
                                                </h1>
                                                <p class="hero-text" data-ani="slideinup" data-ani-delay="0.6s">
                                                    Quality used automotive parts from dismantled vehicles. Browse our catalogue to find what you need at fair prices across New Zealand.
                                                </p>
                                                <div class="btn-group" data-ani="slideinup" data-ani-delay="0.8s">
                                                    <a href="{{ route('parts.index') }}" class="th-btn style2">Browse Parts <i class="fas fa-arrow-up-right"></i></a>
                                                    <a href="{{ route('contact') }}" class="th-btn style3 text-white">Contact Us <i class="fas fa-arrow-up-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-7 col-xl-6 col-lg-6">
                                            <div class="hero-img text-end">
                                                <div class="img-main" data-ani="slideinright" data-ani-delay="0.8s">
                                                    <img src="{{ $img }}" alt="{{ $heroTitles[$i] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
            <div class="slider-pagination"></div>
            <div class="slider-pagination2"></div>
        </div>

        {{-- Search / Reservation bar --}}
        <div class="reservation-area">
            <div class="container th-container">
                <div class="reservation-wrapper">
                    <div class="reservation-form">
                        <form action="{{ route('parts.index') }}" method="GET">
                            <div class="row">
                                <div class="col-12">
                                    <div class="reservation-area-top">
                                        <div class="left">
                                            <h5>Search Parts Now</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="select-group-wrapper">
                                        <div class="form-group">
                                            <input type="text" name="make" class="form-control"
                                                placeholder="Make (e.g. Toyota)" value="{{ request('make') }}">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="model" class="form-control"
                                                placeholder="Model (e.g. Hilux)" value="{{ request('model') }}">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="year" class="form-control"
                                                placeholder="Year (e.g. 2015)" value="{{ request('year') }}">
                                        </div>
                                        <div class="form-group">
                                            <select name="category" class="form-select nice-select">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ request('category') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="advance-btn-wrapper">
                                            <button class="th-btn style2" type="submit">
                                                Search Parts <i class="fas fa-arrow-up-right"></i>
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
    </div>
    {{-- ======== / Hero Section ======== --}}

    @if($manufacturerBrands->isNotEmpty())
        {{-- Our Popular Brands (Kars template + manufacturer logos) --}}
        <div class="brand-area-1 space overflow-hidden">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-7">
                        <div class="title-area text-center">
                            <h2 class="sec-title">Our Popular Brands</h2>
                            <p>Browse used parts by manufacturer, or see which vehicles we are stripping in the yard — both lists use the same make search.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="swiper th-slider" id="brand-slider-home"
                             data-slider-options='{"loop":true,"pagination":false,"navigation":false,"autoplay":{"delay":4500,"disableOnInteraction":false},"breakpoints":{"0":{"slidesPerView":2},"576":{"slidesPerView":2},"768":{"slidesPerView":3},"992":{"slidesPerView":4},"1200":{"slidesPerView":5},"1400":{"slidesPerView":6}}}'>
                            <div class="swiper-wrapper">
                                @foreach($manufacturerBrands as $brand)
                                    <div class="swiper-slide">
                                        <div class="brand-box gm-brand-box">
                                            <a href="{{ route('parts.index', ['make' => $brand['make']]) }}"
                                               class="gm-brand-box-main d-flex flex-column align-items-center justify-content-center text-decoration-none text-reset flex-grow-1">
                                                <img src="{{ asset('images/manufacturers/'.$brand['file']) }}"
                                                     alt="{{ $brand['label'] }}"
                                                     loading="lazy"
                                                     width="140"
                                                     height="80">
                                                <span class="small text-muted mt-2 fw-semibold">{{ $brand['label'] }}</span>
                                            </a>
                                            <a href="{{ route('vehicles.index', ['make' => $brand['make']]) }}"
                                               class="gm-brand-box-yard small text-center text-muted text-decoration-none pb-2">
                                                Now dismantling
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ==============================
         Featured Parts Section
    ============================== --}}
    <section class="feature-sec-1 space" data-bg-src="/kars/img/bg/feature-sec-bg-1.png">
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-center">
                <div class="col-xxl-7 col-xl-9">
                    <div class="title-area text-center text-lg-start">
                        <h2 class="sec-title">Featured Parts Inventory</h2>
                        <p class="pe-lg-5 me-xl-5">Browse our featured used auto parts — quality checked and ready to go. Updated regularly as new vehicles are dismantled.</p>
                    </div>
                </div>
                <div class="col-auto mt-2">
                    <div class="sec-btn">
                        <a href="{{ route('parts.index') }}" class="th-btn style3">
                            View All Parts <i class="fas fa-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row gy-30 justify-content-center">
                @forelse($featuredParts as $part)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="feature-list-1">
                            <div class="box-icon">
                                @if(is_array($part->images) && count($part->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}"
                                         alt="{{ $part->title }}">
                                @else
                                    <img src="/kars/img/featured/featured-1-1.jpg" alt="{{ $part->title }}">
                                @endif
                            </div>
                            <div class="car-content">
                                <div class="media-body">
                                    <h3 class="box-title">
                                        <a href="{{ route('parts.show', $part->slug) }}">{{ $part->title }}</a>
                                    </h3>
                                    <p class="box-text"><span>Category:</span> {{ $part->category->name }}</p>
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
                    <div class="col-12 text-center">
                        <p class="text-body-secondary">No featured parts available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ==============================
         Now Dismantling Section
    ============================== --}}
    @if($dismantling->isNotEmpty())
    <section class="space bg-smoke" data-bg-src="/kars/img/bg/feature-sec-bg-1.png">
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-center">
                <div class="col-xxl-7 col-xl-9">
                    <div class="title-area text-center text-lg-start">
                        <h2 class="sec-title">Now Dismantling</h2>
                        <p class="pe-lg-5 me-xl-5">These vehicles are currently being broken for parts. Click through to see all available parts from each vehicle.</p>
                    </div>
                </div>
                <div class="col-auto mt-2">
                    <div class="sec-btn">
                        <a href="{{ route('vehicles.index') }}" class="th-btn style3">
                            View All Vehicles <i class="fas fa-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row gy-30 justify-content-center">
                @foreach($dismantling as $vehicle)
                    <div class="col-xl-6 col-lg-12">
                        <div class="feature-list-1 list">
                            <div class="box-icon">
                                @if(is_array($vehicle->images) && count($vehicle->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}"
                                         alt="{{ $vehicle->display_name }}">
                                @else
                                    <img src="/kars/img/featured/featured-1-1.jpg" alt="{{ $vehicle->display_name }}">
                                @endif
                            </div>
                            <div class="car-content">
                                <div class="media-body">
                                    <h3 class="box-title">
                                        <a href="{{ route('vehicles.show', $vehicle) }}">{{ $vehicle->display_name }}</a>
                                    </h3>
                                    <p class="box-text"><span>Stock:</span> {{ $vehicle->stock_number ?: 'N/A' }}</p>
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
                                    <a class="th-btn sm style3" href="{{ route('vehicles.show', $vehicle) }}">
                                        View Parts <i class="fas fa-arrow-up-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection
