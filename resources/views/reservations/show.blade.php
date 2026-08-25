@extends('layouts.kars')

@section('title', 'Reservation ' . $reservation->reference)
@section('meta_description', 'Your part reservation with G&M Auto Spares.')

@push('head_styles')
<style>
    .gm-res-card { max-width: 720px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: clamp(1.25rem, 4vw, 2.25rem); box-shadow: 0 10px 30px rgba(15,23,42,.06); }
    .gm-res-tick { width: 56px; height: 56px; border-radius: 50%; background: #d1fae5; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1rem; }
    .gm-res-ref { display: inline-block; padding: .45rem 1rem; border-radius: 999px; background: #0f3460; color: #fff; font-size: 1.15rem; font-weight: 800; letter-spacing: .08em; }
    .gm-res-rows { margin: 1.5rem 0 0; border-top: 1px solid #e5e7eb; }
    .gm-res-row { display: flex; justify-content: space-between; gap: 1rem; padding: .8rem 0; border-bottom: 1px solid #e5e7eb; }
    .gm-res-row dt { color: #6b7280; margin: 0; font-weight: 600; }
    .gm-res-row dd { margin: 0; text-align: right; font-weight: 600; color: #0f172a; }
    .gm-res-pay { margin-top: 1.5rem; padding: 1rem 1.15rem; border-radius: 12px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; }
    .gm-res-pay p { margin: 0; }
    .gm-res-status { display: inline-block; padding: .2em .7em; border-radius: 999px; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .gm-res-status--reserved  { background: #fef3c7; color: #92400e; }
    .gm-res-status--collected { background: #d1fae5; color: #065f46; }
    .gm-res-status--cancelled { background: #e5e7eb; color: #374151; }
    .gm-res-status--expired   { background: #fee2e2; color: #991b1b; }
    .gm-res-actions { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.5rem; }
</style>
@endpush

@section('content')
    <div class="breadcumb-wrapper style-2 compact-header" data-bg-src="/images/page-headers/Parts-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Reservation</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>{{ $reservation->reference }}</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="space-extra-bottom" style="padding-top: 60px;">
        <div class="container">
            <div class="gm-res-card">
                @if($reservation->status === \App\Enums\ReservationStatus::Reserved)
                    <div class="gm-res-tick"><i class="fas fa-check"></i></div>
                    <h2 class="mb-2" style="font-size:1.7rem;">Your part is reserved</h2>
                    <p class="mb-3">Thanks {{ $reservation->name }} — we have put it aside for you and emailed you a copy of this confirmation.</p>
                @else
                    <h2 class="mb-2" style="font-size:1.7rem;">Reservation {{ $reservation->status->label() }}</h2>
                    <p class="mb-3">This reservation is no longer active.</p>
                @endif

                <span class="gm-res-ref">{{ $reservation->reference }}</span>

                <dl class="gm-res-rows">
                    <div class="gm-res-row">
                        <dt>Part</dt>
                        <dd>
                            @if($reservation->part)
                                <a href="{{ route('parts.show', $reservation->part->slug) }}">{{ $reservation->part_title }}</a>
                            @else
                                {{ $reservation->part_title }}
                            @endif
                        </dd>
                    </div>
                    @if($reservation->part_price)
                        <div class="gm-res-row">
                            <dt>Price</dt>
                            <dd>${{ number_format((float) $reservation->part_price, 2) }} NZD</dd>
                        </div>
                    @endif
                    <div class="gm-res-row">
                        <dt>Status</dt>
                        <dd><span class="gm-res-status gm-res-status--{{ $reservation->status->value }}">{{ $reservation->status->label() }}</span></dd>
                    </div>
                    @if($reservation->status === \App\Enums\ReservationStatus::Reserved && $reservation->expires_at)
                        <div class="gm-res-row">
                            <dt>Please collect by</dt>
                            <dd>{{ $reservation->expires_at->format('l j F Y') }}</dd>
                        </div>
                    @endif
                    <div class="gm-res-row">
                        <dt>Reserved on</dt>
                        <dd>{{ $reservation->created_at->format('j F Y') }}</dd>
                    </div>
                </dl>

                @if($reservation->status === \App\Enums\ReservationStatus::Reserved)
                    <div class="gm-res-pay">
                        <p><strong>Nothing to pay now.</strong> You pay when you collect the part from our Te Awamutu yard. Please bring your reference number.</p>
                    </div>
                @endif

                <div class="gm-res-actions">
                    <a href="tel:+6478498814" class="th-btn style-2"><i class="fa-solid fa-phone"></i> 07 849 8814</a>
                    <a href="{{ route('parts.index') }}" class="th-btn style3 dealer">Browse More Parts <i class="fas fa-arrow-up-right"></i></a>
                </div>

                <p class="mt-4 mb-0 text-body-secondary" style="font-size:.9rem;">
                    See our <a href="/returns-policy">Returns Policy</a> and <a href="/shipping-policy">Shipping Policy</a>.
                </p>
            </div>
        </div>
    </section>
@endsection
