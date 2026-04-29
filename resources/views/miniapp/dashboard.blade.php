@extends('miniapp.layout')
@section('title', 'Add parts & vehicles')

@section('content')
    <div class="card-app">
        <h1 class="page-title">Quick Actions</h1>
        <p class="lead-text">Choose what to add to the website.</p>
        <a href="{{ route('app.parts.create') }}" class="btn-app btn-primary-app">Add a Part</a>
        <a href="{{ route('app.vehicles.create') }}" class="btn-app btn-secondary-app">Add a Vehicle (Now Dismantling)</a>
    </div>
    <p style="margin-top: 1rem;"><a href="{{ route('app.logout') }}" class="muted-link">Log out</a></p>
@endsection
