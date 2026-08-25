@extends('layouts.kars')
@section('title', 'Your Cart')
@include('partials.shop-styles')

@section('content')
    <div class="breadcumb-wrapper style-2 compact-header" data-bg-src="/images/page-headers/Parts-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Your Cart</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Cart</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="space-extra-bottom" style="padding-top:60px;">
        <div class="container gm-shop">
            @if(session('success'))<div class="gm-ok">{{ session('success') }}</div>@endif
            @if($errors->has('cart'))<div class="gm-err">{{ $errors->first('cart') }}</div>@endif

            @if($unavailable->isNotEmpty())
                <div class="gm-warn">
                    <strong>Some items are no longer available</strong> and have not been included in your total:
                    {{ $unavailable->pluck('title')->join(', ') }}.
                </div>
            @endif

            @if($items->isEmpty())
                <div class="gm-shop-card text-center">
                    <h3 style="font-size:1.3rem;">Your cart is empty</h3>
                    <p class="gm-muted">Browse our catalogue and add the parts you need.</p>
                    <a href="{{ route('parts.index') }}" class="th-btn">Browse Parts <i class="fas fa-arrow-up-right"></i></a>
                </div>
            @else
                <div class="gm-shop-card">
                    @foreach($items as $part)
                        <div class="gm-line">
                            @if(is_array($part->images) && count($part->images))
                                <img class="gm-line-img" src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}" alt="">
                            @else
                                <div class="gm-line-img"></div>
                            @endif
                            <div class="gm-line-body">
                                <p class="gm-line-title"><a href="{{ route('parts.show', $part->slug) }}">{{ $part->title }}</a></p>
                                <p class="gm-line-meta">
                                    {{ collect([$part->year, $part->make, $part->model])->filter()->join(' ') ?: $part->category?->name }}
                                    @if($part->shipping_band) &middot; {{ $part->shipping_band->shortLabel() }} @endif
                                </p>
                                @unless($part->isPurchasable())
                                    <p class="gm-line-meta" style="color:#b91c1c;">No longer available</p>
                                @endunless
                                <form method="post" action="{{ route('cart.remove', $part->id) }}" style="margin-top:.3rem;">
                                    @csrf
                                    <button type="submit" class="gm-remove">Remove</button>
                                </form>
                            </div>
                            <div class="gm-line-price">
                                @if($part->price) ${{ number_format((float) $part->price, 2) }} @else POA @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="gm-totals">
                        <div class="gm-total-row">
                            <span>Subtotal</span>
                            <strong>${{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="gm-total-row gm-muted">
                            <span>Freight</span>
                            <span>
                                @if($band)
                                    Calculated at checkout ({{ $band->shortLabel() }} rate)
                                @else
                                    Free collection
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($items->contains(fn($p) => $p->isPurchasable()))
                        <a href="{{ route('checkout.show') }}" class="th-btn w-100 mt-3">
                            Checkout <i class="fas fa-arrow-up-right"></i>
                        </a>
                    @endif
                    <a href="{{ route('parts.index') }}" class="th-btn style3 dealer w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
