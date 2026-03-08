@extends('layouts.app')

@section('title', 'Parts Catalogue')
@section('meta_description', 'Browse our catalogue of quality used car parts. Filter by make, model, year and category.')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="section-title mb-2">Parts catalogue</h1>
            <p class="text-body-secondary mb-0">Use the filters to find the part you need. Results update as you change filters.</p>
        </div>
        @livewire('parts-filter')
    </div>
@endsection
