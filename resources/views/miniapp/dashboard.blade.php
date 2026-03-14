@extends('miniapp.layout')
@section('title', 'Add parts & vehicles')

@section('content')
    <p style="color: #666; margin-bottom: 1.5rem;">Choose what to add to the website.</p>
    <a href="{{ route('app.parts.create') }}" class="btn-app btn-primary-app">Add a part</a>
    <a href="{{ route('app.vehicles.create') }}" class="btn-app btn-secondary-app">Add a vehicle (Now Dismantling)</a>
    <p style="margin-top: 2rem;"><a href="{{ route('app.logout') }}" style="color: #666; font-size: 0.9rem;">Log out</a></p>
@endsection
