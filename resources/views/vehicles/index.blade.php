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

    @livewire('vehicles-filter')
@endsection
