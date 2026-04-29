@extends('layouts.kars')

@section('title', 'Parts Catalogue')
@section('meta_description', 'Browse our catalogue of quality used car parts. Filter by make, model, year and category.')

@section('content')

    {{-- Breadcrumb --}}
    <div class="breadcumb-wrapper" data-bg-src="/kars/img/bg/breadcrumb-bg.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Parts Catalogue</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Parts</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Left Sidebar + Results --}}
    <section class="left-sidebar-details space">
        <div class="container">
            @livewire('parts-filter')
        </div>
    </section>

@endsection
