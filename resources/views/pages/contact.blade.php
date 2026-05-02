@extends('layouts.kars')

@section('title', 'Contact Us')
@section('meta_description', $page->meta_description ?? 'Contact G&M Autospares for parts enquiries.')

@section('content')
    <div class="breadcumb-wrapper" data-bg-src="/images/page-headers/header-1.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Contact</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="contact-area-2 space-top" id="contact-sec">
        <div class="container">
            <div class="title-area text-center">
                <h2 class="sec-title">Our Contact Information</h2>
                @if($page?->content)
                    <p class="mb-0">{!! $page->content !!}</p>
                @endif
            </div>
            <div class="row gy-4 justify-content-center">
                <div class="col-xl-4 col-lg-6 contact-feature-wrap">
                    <div class="contact-feature">
                        <div class="contact-feature-icon">
                            <i class="fa-sharp fa-regular fa-location-dot"></i>
                        </div>
                        <div class="media-body">
                            <p class="contact-feature_label">Our Address</p>
                            <a href="https://www.google.com/maps/search/?api=1&query=2+Bruce+Berquist+Drive,+Te+Awamutu" class="contact-feature_link">
                                2 Bruce Berquist Drive, Te Awamutu
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 contact-feature-wrap">
                    <div class="contact-feature">
                        <div class="contact-feature-icon">
                            <i class="fa-regular fa-phone"></i>
                        </div>
                        <div class="media-body">
                            <p class="contact-feature_label">Phone</p>
                            <a href="tel:+6478718575" class="contact-feature_link">Phone: (07) 871-8575</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 contact-feature-wrap">
                    <div class="contact-feature">
                        <div class="contact-feature-icon">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <div class="media-body">
                            <p class="contact-feature_label">Email</p>
                            <a href="mailto:gmautospares@xtra.co.nz" class="contact-feature_link">gmautospares@xtra.co.nz</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-form-area">
        <div class="container">
            <div class="row gx-0">
                <div class="col-xl-12">
                    <div class="contact-all-wrapper">
                        <div class="row gy-30 align-items-center">
                            <div class="col-lg-7">
                                <div class="contact-form-wrap">
                                    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                                        @csrf
                                        <h3 class="form-title">Get In Touch</h3>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Your Name *" required value="{{ old('name') }}">
                                                @error('name')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Address *" required value="{{ old('email') }}">
                                                @error('email')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone Number" value="{{ old('phone') }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject (optional)" value="{{ old('subject') }}">
                                            </div>
                                            <div class="form-group col-12">
                                                <textarea name="message" id="message" cols="30" rows="4" class="form-control" placeholder="Your Message *" required>{{ old('message') }}</textarea>
                                                @error('message')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-btn col-12">
                                                <button class="th-btn star-btn" type="submit">Submit Message</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="contact-form-thumb overflow-hidden">
                                    <img src="/kars/img/contact/contact-page-thumb.jpg" alt="Contact G&M Auto Parts">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-top">
        <div class="container-fluid p-0">
            <div class="contact-map">
                @if(config('services.google_maps_embed'))
                    {!! config('services.google_maps_embed') !!}
                @else
                    <iframe src="https://www.google.com/maps?q=2+Bruce+Berquist+Drive,+Te+Awamutu&output=embed" allowfullscreen loading="lazy"></iframe>
                @endif
            </div>
        </div>
    </div>
@endsection
