@extends('layouts.app')

@section('title', 'Now Dismantling')
@section('meta_description', 'Vehicles we are currently dismantling. Browse parts available from each vehicle.')

@section('content')
    <div class="container">
        <h1 class="h2 mb-2">Now dismantling</h1>
        <p class="text-body-secondary mb-4">Select a vehicle to see available parts.</p>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
            @foreach($vehicles as $vehicle)
                <a href="{{ route('vehicles.show', $vehicle) }}" class="vehicle-card card h-100 text-decoration-none text-body">
                    <div class="vehicle-card-image card-img-top">
                        @if(is_array($vehicle->images) && count($vehicle->images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->images[0]) }}" alt="{{ $vehicle->display_name }}" loading="lazy" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="part-card-placeholder">No image</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h3 class="card-title h6">{{ $vehicle->display_name }}</h3>
                        <p class="card-text small text-body-secondary mb-1">
                            @if($vehicle->engine) {{ $vehicle->engine }} @endif
                            @if($vehicle->transmission) · {{ $vehicle->transmission }} @endif
                            @if($vehicle->stock_number) · Stock: {{ $vehicle->stock_number }} @endif
                        </p>
                        <p class="card-text small text-body-secondary mb-0">{{ $vehicle->parts_count }} parts</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="pagination-wrap mt-4">
            {{ $vehicles->links() }}
        </div>
    </div>
@endsection
