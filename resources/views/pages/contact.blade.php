@extends('layouts.app')

@section('title', 'Contact Us')
@section('meta_description', $page->meta_description ?? 'Contact G&M Autospares for parts enquiries.')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-3">Contact us</h1>
                @if($page->content)
                    <div class="content-body mb-4">{!! $page->content !!}</div>
                @endif
                <form action="{{ route('contact.submit') }}" method="post" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Your name *</label>
                        <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
                        @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
                        @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
                        @error('message')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send message</button>
                </form>
                @if(config('services.google_maps_embed'))
                    <div class="ratio ratio-16x9 rounded overflow-hidden border">
                        {!! config('services.google_maps_embed') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
