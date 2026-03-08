@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'G&M Autospares - quality used car parts in New Zealand. Browse parts and vehicles we are now dismantling.')

@section('content')
    @php
        $slides = \App\Models\SliderSlide::where('is_active', true)->orderBy('sort_order')->get();
        $categories = \App\Models\PartCategory::where('is_active', true)->orderBy('sort_order')->get();
    @endphp

    @if($slides->isNotEmpty())
        {{-- Hero: slider with overlay (Travely style) --}}
        <section class="hero-slider travely-hero has-bg-image" aria-label="Hero slider">
            @foreach($slides as $index => $slide)
                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($slide->image) }}" alt="{{ $slide->title ?? 'Slide' }}">
                    <div class="hero-overlay"></div>
                    <div class="hero-slide-content position-absolute bottom-0 start-0 end-0 p-4 text-white text-start text-md-center">
                        @if($slide->title)<h2 class="h3 mb-1">{{ $slide->title }}</h2>@endif
                        @if($slide->subtitle)<p class="mb-0 opacity-90">{{ $slide->subtitle }}</p>@endif
                        @if($slide->link_url && $slide->link_text)
                            <a href="{{ $slide->link_url }}" class="btn btn-primary btn-sm mt-2">{{ $slide->link_text }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
        @if($slides->count() > 1)
            <script>
                (function(){
                    var slides = document.querySelectorAll('.hero-slider .hero-slide');
                    var i = 0;
                    setInterval(function(){
                        slides[i].classList.remove('active');
                        i = (i + 1) % slides.length;
                        slides[i].classList.add('active');
                    }, 5000);
                })();
            </script>
        @endif
    @else
        {{-- Static hero (no slider) – Travely style --}}
        <section class="travely-hero">
            <div class="hero-overlay"></div>
            <div class="hero-inner">
                <h1>Find Quality Auto Parts</h1>
                <p class="lead">Search our catalogue for used car parts across makes and models. Quality parts at fair prices across New Zealand.</p>
            </div>
        </section>
    @endif

    {{-- Flight-style search card (overlaps hero) – like “Search flights” for parts --}}
    <div class="container travely-search-card">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('parts.index') }}" method="get" class="row g-3 align-items-end">
                    <div class="col-6 col-md">
                        <label for="home-make" class="form-label">Make</label>
                        <input type="text" id="home-make" name="make" class="form-control" placeholder="e.g. Toyota" value="{{ request('make') }}">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-model" class="form-label">Model</label>
                        <input type="text" id="home-model" name="model" class="form-control" placeholder="e.g. Hilux" value="{{ request('model') }}">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-year" class="form-label">Year</label>
                        <input type="text" id="home-year" name="year" class="form-control" placeholder="e.g. 2015" value="{{ request('year') }}">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-category" class="form-label">Category</label>
                        <select id="home-category" name="category" class="form-select">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md">
                        <label for="home-keyword" class="form-label">Keyword</label>
                        <input type="text" id="home-keyword" name="keyword" class="form-control" placeholder="Search parts..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto btn-search">
                            Search parts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Featured parts & Now dismantling – Travely-style sections --}}
    <section class="py-5 bg-light">
        <div class="container">
            @php
                $featuredParts = \App\Models\Part::where('is_visible', true)->where('is_featured', true)->latest()->take(6)->get();
            @endphp
            @if($featuredParts->isNotEmpty())
                <h2 class="section-title">Featured parts</h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                    @foreach($featuredParts as $part)
                        <a href="{{ route('parts.show', $part->slug) }}" class="part-card card h-100 text-decoration-none text-dark">
                            <div class="part-card-image card-img-top">
                                @if(is_array($part->images) && count($part->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}" alt="{{ $part->title }}" loading="lazy" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="part-card-placeholder">No image</div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $part->title }}</h5>
                                <p class="card-text small text-body-secondary">{{ $part->category->name }}</p>
                                @if($part->price)<p class="part-card-price mb-0">${{ number_format($part->price, 2) }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('parts.index') }}" class="btn btn-primary">View all parts</a>
                </div>
            @endif

            @php
                $dismantling = \App\Models\Vehicle::where('is_visible', true)->withCount('parts')->latest()->take(4)->get();
            @endphp
            @if($dismantling->isNotEmpty())
                <h2 class="section-title mt-5 pt-4">Now dismantling</h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    @foreach($dismantling as $vehicle)
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="vehicle-card card h-100 text-decoration-none text-dark">
                            <div class="vehicle-card-image card-img-top">
                                @if(is_array($vehicle->images) && count($vehicle->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}" alt="{{ $vehicle->display_name }}" loading="lazy" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="part-card-placeholder">No image</div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $vehicle->display_name }}</h5>
                                <p class="card-text small text-body-secondary mb-0">{{ $vehicle->parts_count }} parts</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">View all vehicles</a>
                </div>
            @endif
        </div>
    </section>
@endsection
