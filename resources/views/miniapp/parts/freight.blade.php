@extends('miniapp.layout')
@section('title', 'Freight size')

@section('content')
    <div class="card-app">
        <h1 class="page-title">Freight Size</h1>
        <p class="lead-text">
            <strong>{{ $part->title }}</strong><br>
            {{ collect([$part->stock_number ? '#' . $part->stock_number : null, $part->year, $part->make, $part->model])->filter()->implode(' · ') ?: 'No stock details' }}
        </p>

        <form method="post" action="{{ route('app.stock.freight.update', $part) }}">
            @csrf
            <input type="hidden" name="return_to" value="{{ $returnTo }}">

            @include('miniapp.partials.freight-size', ['selected' => old('shipping_band', $part->shipping_band?->value)])

            <button type="submit" class="btn-app btn-primary-app">Save</button>
        </form>
    </div>

    <p style="margin-top: 1rem;">
        <a href="{{ $returnTo }}" class="back-link">&larr; Back to stock</a>
    </p>
@endsection
