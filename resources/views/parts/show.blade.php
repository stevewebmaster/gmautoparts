@extends('layouts.kars')

@section('title', $part->title)
@section('meta_description', Str::limit(strip_tags($part->description), 160))

@section('content')
    <div class="breadcumb-wrapper style-2" data-bg-src="/images/page-headers/Parts-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $part->title }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('parts.index') }}">Parts</a></li>
                    <li>{{ $part->title }}</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="th-inventory-wrapper inventory-details space-extra-bottom">
        <div class="container">
            <div class="row gy-4 gx-40">

                {{-- Main content --}}
                <div class="col-xxl-8 col-lg-7">

                    {{-- Image gallery --}}
                    @if(is_array($part->images) && count($part->images))
                        <div class="mb-4">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}"
                                 alt="{{ $part->title }}"
                                 id="part-main-image"
                                 class="img-fluid w-100 rounded"
                                 style="max-height: 480px; object-fit: cover;">
                            @if(count($part->images) > 1)
                                <div class="d-flex gap-2 mt-2 flex-wrap">
                                    @foreach($part->images as $img)
                                        <button type="button"
                                                onclick="document.getElementById('part-main-image').src='{{ \Illuminate\Support\Facades\Storage::url($img) }}'"
                                                class="p-0 border-0 rounded overflow-hidden"
                                                style="width:80px;height:60px;cursor:pointer;">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}"
                                                 alt="" class="w-100 h-100" style="object-fit:cover;">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Part title + feature strip --}}
                    <div class="th-inventory inventory-single">
                        <div class="inventory-single-top">
                            <div class="media-body">
                                <h2 class="box-title">{{ $part->title }}</h2>
                                <p class="box-text">
                                    <span>{{ $part->category->name }}</span>
                                    @if($part->subcategory) &mdash; {{ $part->subcategory->name }} @endif
                                </p>
                            </div>
                            <ul class="car-feature">
                                @if($part->condition)
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-3.svg" alt="Condition"></div>
                                        <p>{{ $part->condition }}</p>
                                    </li>
                                @endif
                                @if($part->stock_number)
                                    <li>
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-14.svg" alt="Stock"></div>
                                        <p>{{ $part->stock_number }}</p>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @if($part->price)
                            <div class="single-inventory-content">
                                <h5 class="title" style="font-size:2rem; color: var(--theme-color);">
                                    ${{ number_format($part->price, 2) }}
                                </h5>
                            </div>
                        @endif

                        @if($part->description)
                            <div class="single-inventory-content">
                                <h5 class="title">Description</h5>
                                <p>{!! nl2br(e($part->description)) !!}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Part overview --}}
                    <div class="vehicle-overview-wrap">
                        <h5 class="title">Part Details</h5>
                        <div class="vehicle-overview-body">
                            <div class="overview-item">
                                <div class="left">
                                    <div class="icon"><img src="/kars/img/icon/inventory-details-1-1.svg" alt="icon"></div>
                                    <p>Category</p>
                                </div>
                                <div class="right"><p>{{ $part->category->name }}</p></div>
                            </div>
                            @if($part->subcategory)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-1.svg" alt="icon"></div>
                                        <p>Subcategory</p>
                                    </div>
                                    <div class="right"><p>{{ $part->subcategory->name }}</p></div>
                                </div>
                            @endif
                            @if($part->condition)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-3.svg" alt="icon"></div>
                                        <p>Condition</p>
                                    </div>
                                    <div class="right"><p>{{ $part->condition }}</p></div>
                                </div>
                            @endif
                            @if($part->stock_number)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/inventory-details-1-14.svg" alt="icon"></div>
                                        <p>Stock #</p>
                                    </div>
                                    <div class="right"><p>{{ $part->stock_number }}</p></div>
                                </div>
                            @endif
                            @if($part->vehicle)
                                <div class="overview-item">
                                    <div class="left">
                                        <div class="icon"><img src="/kars/img/icon/car-feature-icon-1-1.svg" alt="icon"></div>
                                        <p>From Vehicle</p>
                                    </div>
                                    <div class="right">
                                        <p><a href="{{ route('vehicles.show', $part->vehicle) }}" class="text-inherit">{{ $part->vehicle->display_name }}</a></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="col-xxl-4 col-lg-5">
                    <aside class="sidebar-area">

                        {{-- Enquiry form --}}
                        <div class="widget widget-style-smoke shadow-style">
                            <div class="inventory-owner-wrap">
                                <div class="inventory-owner-top">
                                    <div class="content">
                                        <h6 class="box-title">Enquire About This Part</h6>
                                        <p class="saving">{{ $part->title }}</p>
                                    </div>
                                </div>
                                <div class="contact-form style-3 mt-30">
                                    <form action="{{ route('parts.enquire') }}" method="post" class="ajax-contact">
                                        @csrf
                                        <input type="hidden" name="part_id" value="{{ $part->id }}">
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label for="enq-name">Your name *</label>
                                                <input type="text" id="enq-name" name="name" class="form-control pill"
                                                       placeholder="Your name" required value="{{ old('name') }}">
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="enq-email">Email *</label>
                                                <input type="email" id="enq-email" name="email" class="form-control pill"
                                                       placeholder="Your email" required value="{{ old('email') }}">
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="enq-phone">Phone</label>
                                                <input type="text" id="enq-phone" name="phone" class="form-control pill"
                                                       placeholder="Your phone" value="{{ old('phone') }}">
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="enq-message">Message</label>
                                                <textarea id="enq-message" name="message" class="form-control"
                                                          rows="3" placeholder="Your message">{{ old('message') }}</textarea>
                                            </div>
                                            <div class="form-btn col-12">
                                                <button type="submit" class="th-btn w-100">
                                                    Send Enquiry <i class="fas fa-arrow-up-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @if(session('success'))
                                            <p class="form-messages mb-0 mt-3 text-success">{{ session('success') }}</p>
                                        @endif
                                    </form>
                                </div>
                                <div class="inventory-owner-bottom mt-3">
                                    <a href="tel:+6478498814" class="th-btn style-2 w-100">
                                        <i class="fa-solid fa-phone"></i> 07 849 8814
                                    </a>
                                    @if($part->vehicle)
                                        <a href="{{ route('vehicles.show', $part->vehicle) }}" class="th-btn style3 dealer w-100">
                                            View Dismantling Vehicle <i class="fas fa-arrow-up-right"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('parts.index') }}" class="th-btn style3 dealer w-100">
                                        <i class="fas fa-arrow-left"></i> Back to Parts
                                    </a>
                                </div>
                            </div>
                        </div>

                    </aside>
                </div>

            </div>
        </div>
    </section>
@endsection
