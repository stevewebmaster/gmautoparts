@extends('layouts.app')

@section('title', $vehicle->display_name . ' - Now Dismantling')
@section('meta_description', 'Parts available from ' . $vehicle->display_name . '.')

@section('content')
    <div class="container">
        <a href="{{ route('vehicles.index') }}" class="text-body-secondary text-decoration-none small d-inline-block mb-3">← Back to Now Dismantling</a>
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                @if(is_array($vehicle->images) && count($vehicle->images))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}" alt="{{ $vehicle->display_name }}" class="img-fluid rounded border">
                @endif
            </div>
            <div class="col-md-6">
                <h1 class="h2 mb-2">{{ $vehicle->display_name }}</h1>
                <p class="text-body-secondary small mb-0">@if($vehicle->engine) Engine: {{ $vehicle->engine }}<br>@endif
                    @if($vehicle->transmission) Transmission: {{ $vehicle->transmission }}<br>@endif
                    @if($vehicle->stock_number) Stock #: {{ $vehicle->stock_number }} @endif</p>
            </div>
        </div>
        <h2 class="h5 mb-3">Parts from this vehicle</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
            @forelse($parts as $part)
                <a href="{{ route('parts.show', $part->slug) }}" class="part-card card h-100 text-decoration-none text-body">
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
            @empty
                <p class="text-body-secondary text-center py-4 col-12">No parts listed yet for this vehicle.</p>
            @endforelse
        </div>
        <div class="pagination-wrap mt-4">
            {{ $parts->links() }}
        </div>
    </div>
@endsection
