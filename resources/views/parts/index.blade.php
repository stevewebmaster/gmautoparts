@extends('layouts.kars')

@section('title', 'Parts Catalogue')
@section('meta_description', 'Browse our catalogue of quality used car parts. Filter by make, model, year and category.')

@section('content')

    {{-- Breadcrumb --}}
    <div class="breadcumb-wrapper style-2" data-bg-src="/images/page-headers/header-1.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Inventory Grid</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Inventory</li>
                </ul>
            </div>
        </div>
    </div>

    @livewire('parts-filter')

@endsection
