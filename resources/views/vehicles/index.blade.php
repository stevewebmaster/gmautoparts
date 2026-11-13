@extends('layouts.kars')

@section('full_title', 'Cars We Are Wrecking Now – Used Parts | G&M Autospares, Te Awamutu')
@section('meta_description', 'The cars G&M Autospares is wrecking now in Te Awamutu, Waikato. Browse each one for used parts, courier NZ-wide or pick up. Phone ' . config('contact.phone') . '.')

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
