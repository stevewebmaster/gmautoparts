@extends('miniapp.layout')
@section('title', 'Add a vehicle')

@section('content')
    <h1 style="font-size: 1.35rem; margin-bottom: 1rem;">Add a vehicle (Now Dismantling)</h1>
    <form method="post" action="{{ route('app.vehicles.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="make">Make *</label>
            <input type="text" id="make" name="make" value="{{ old('make') }}" required placeholder="e.g. Holden">
        </div>
        <div class="form-group">
            <label for="model">Model *</label>
            <input type="text" id="model" name="model" value="{{ old('model') }}" required placeholder="e.g. Commodore">
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <input type="text" id="year" name="year" value="{{ old('year') }}" placeholder="e.g. 2012">
        </div>
        <div class="form-group">
            <label for="engine">Engine</label>
            <input type="text" id="engine" name="engine" value="{{ old('engine') }}" placeholder="e.g. 3.6 V6">
        </div>
        <div class="form-group">
            <label for="transmission">Transmission</label>
            <input type="text" id="transmission" name="transmission" value="{{ old('transmission') }}" placeholder="e.g. Auto, Manual">
        </div>
        <div class="form-group">
            <label for="stock_number">Stock number</label>
            <input type="text" id="stock_number" name="stock_number" value="{{ old('stock_number') }}">
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" placeholder="Any extra details">{{ old('notes') }}</textarea>
        </div>
        <div class="form-group">
            <label for="images">Photos *</label>
            <input type="file" id="images" name="images[]" accept="image/*" capture="environment" multiple>
            <p class="photo-hint">Take or select at least one photo. On phone you can use camera.</p>
        </div>
        <button type="submit" class="btn-app btn-primary-app">Save vehicle</button>
    </form>
    <p style="margin-top: 1rem;"><a href="{{ route('app.dashboard') }}" style="color: #666; font-size: 0.9rem;">← Back</a></p>
@endsection
