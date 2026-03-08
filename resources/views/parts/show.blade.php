@extends('layouts.app')

@section('title', $part->title)
@section('meta_description', Str::limit(strip_tags($part->description), 160))

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-6 part-detail-gallery">
                @if(is_array($part->images) && count($part->images))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}" alt="{{ $part->title }}" id="part-main-image" class="img-fluid rounded border">
                    @if(count($part->images) > 1)
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            @foreach($part->images as $i => $img)
                                <button type="button" onclick="document.getElementById('part-main-image').src='{{ \Illuminate\Support\Facades\Storage::url($img) }}'" class="btn p-0 border rounded overflow-hidden" style="width:60px;height:60px;">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}" alt="" class="w-100 h-100 object-fit-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="part-card-placeholder ratio ratio-4x3 rounded border d-flex align-items-center justify-content-center">No image</div>
                @endif
            </div>
            <div class="col-lg-6">
                <h1 class="h2 mb-2">{{ $part->title }}</h1>
                <p class="text-body-secondary small mb-2">
                    {{ $part->category->name }}
                    @if($part->subcategory) · {{ $part->subcategory->name }} @endif
                    @if($part->vehicle) · Vehicle: {{ $part->vehicle->display_name }} @endif
                </p>
                @if($part->stock_number)<p class="text-body-secondary small mb-1"><strong>Stock #:</strong> {{ $part->stock_number }}</p>@endif
                @if($part->condition)<p class="text-body-secondary small mb-2"><strong>Condition:</strong> {{ $part->condition }}</p>@endif
                @if($part->price)<p class="fs-4 fw-bold text-primary mb-3">${{ number_format($part->price, 2) }}</p>@endif
                @if($part->description)
                    <div class="mb-4">{!! nl2br(e($part->description)) !!}</div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <h3 class="h6 mb-3">Enquire about this part</h3>
                        <form action="{{ route('parts.enquire') }}" method="post">
                            @csrf
                            <input type="hidden" name="part_id" value="{{ $part->id }}">
                            <div class="mb-2"><label for="enq-name" class="form-label">Your name *</label><input type="text" id="enq-name" name="name" class="form-control" required value="{{ old('name') }}"></div>
                            <div class="mb-2"><label for="enq-email" class="form-label">Email *</label><input type="email" id="enq-email" name="email" class="form-control" required value="{{ old('email') }}"></div>
                            <div class="mb-2"><label for="enq-phone" class="form-label">Phone</label><input type="text" id="enq-phone" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                            <div class="mb-3"><label for="enq-message" class="form-label">Message</label><textarea id="enq-message" name="message" class="form-control" rows="3">{{ old('message') }}</textarea></div>
                            <button type="submit" class="btn btn-primary">Enquire now</button>
                        </form>
                        @if(session('success'))<p class="text-success small mt-2 mb-0">{{ session('success') }}</p>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
