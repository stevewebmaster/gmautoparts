@extends('layouts.kars')
@section('title', 'Order ' . $order->reference)
@include('partials.shop-styles')

@section('content')
    <div class="breadcumb-wrapper style-2 compact-header" data-bg-src="/images/page-headers/Parts-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Order {{ $order->reference }}</h1>
            </div>
        </div>
    </div>

    <section class="space-extra-bottom" style="padding-top:60px;">
        <div class="container gm-shop">
            <div class="gm-shop-card">
                @if($order->isPaid())
                    <div class="gm-ok"><strong>Payment received.</strong> Thanks {{ $order->name }} — we have emailed your receipt.</div>
                @else
                    <div class="gm-warn">
                        <strong>We haven't confirmed your payment yet.</strong>
                        If you have just paid, this usually settles within a minute — refresh the page.
                        Nothing further is needed from you. If it stays like this, call us on 07 849 8814
                        quoting {{ $order->reference }}.
                    </div>
                @endif

                <h3 style="font-size:1.15rem;margin:1rem 0 .5rem;">Your order</h3>
                @foreach($order->items as $item)
                    <div class="gm-line">
                        <div class="gm-line-body">
                            <p class="gm-line-title">{{ $item->title }}</p>
                            @if($item->part)
                                <p class="gm-line-meta"><a href="{{ route('parts.show', $item->part->slug) }}">View part</a></p>
                            @endif
                        </div>
                        <div class="gm-line-price">${{ number_format((float) $item->price, 2) }}</div>
                    </div>
                @endforeach

                <div class="gm-totals">
                    <div class="gm-total-row"><span>Subtotal</span><strong>${{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div class="gm-total-row"><span>{{ $order->isPickup() ? 'Collection' : 'Freight' }}</span><strong>${{ number_format((float) $order->shipping, 2) }}</strong></div>
                    <div class="gm-total-row gm-total-row--grand"><span>Total</span><span>${{ number_format((float) $order->total, 2) }}</span></div>
                </div>

                <h3 style="font-size:1.15rem;margin:1.5rem 0 .5rem;">
                    {{ $order->isPickup() ? 'Collecting' : 'Delivery' }}
                </h3>
                @if($order->isPickup())
                    <p class="mb-0">Your parts are set aside at our Te Awamutu yard. Bring your order number
                        <strong>{{ $order->reference }}</strong>. There is nothing further to pay.</p>
                @else
                    <p class="mb-0">
                        {{ $order->address_line1 }}@if($order->address_line2), {{ $order->address_line2 }}@endif<br>
                        @if($order->suburb){{ $order->suburb }}<br>@endif
                        {{ $order->city }} {{ $order->postcode }}@if($order->is_rural) <em>(rural)</em>@endif
                    </p>
                    <p class="gm-muted mt-2 mb-0">We will email you when your order is on its way.</p>
                @endif

                <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1.5rem;">
                    <a href="tel:+6478498814" class="th-btn style-2"><i class="fa-solid fa-phone"></i> 07 849 8814</a>
                    <a href="{{ route('parts.index') }}" class="th-btn style3 dealer">Browse More Parts <i class="fas fa-arrow-up-right"></i></a>
                </div>

                <p class="gm-muted mt-4 mb-0">
                    <a href="/returns-policy">Returns Policy</a> &middot; <a href="/shipping-policy">Shipping Policy</a>
                </p>
            </div>
        </div>
    </section>
@endsection
