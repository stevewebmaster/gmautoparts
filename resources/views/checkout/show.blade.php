@extends('layouts.kars')
@section('title', 'Checkout')
@include('partials.shop-styles')

@section('content')
    <div class="breadcumb-wrapper style-2 compact-header" data-bg-src="/images/page-headers/Parts-Header.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Checkout</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li>Checkout</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="space-extra-bottom" style="padding-top:60px;">
        <div class="container gm-shop">
            @if($errors->has('checkout'))<div class="gm-err">{{ $errors->first('checkout') }}</div>@endif
            @if($errors->any() && ! $errors->has('checkout'))
                <div class="gm-err">Please check the highlighted fields below.</div>
            @endif

            <form method="post" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf
                <div class="row gy-4">
                    <div class="col-lg-7">
                        <div class="gm-shop-card">
                            <fieldset class="gm-fieldset">
                                <legend>Your details</legend>
                                <div class="form-group">
                                    <label for="co-name">Name *</label>
                                    <input id="co-name" name="name" type="text" class="form-control pill" required value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="co-email">Email *</label>
                                    <input id="co-email" name="email" type="email" class="form-control pill" required value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <label for="co-phone">Phone</label>
                                    <input id="co-phone" name="phone" type="text" class="form-control pill" value="{{ old('phone') }}">
                                </div>
                            </fieldset>

                            <fieldset class="gm-fieldset">
                                <legend>How would you like it?</legend>
                                <div class="gm-radio-row">
                                    <label class="gm-radio">
                                        <input type="radio" name="fulfilment" value="delivery" data-fulfilment
                                               {{ old('fulfilment', 'delivery') === 'delivery' ? 'checked' : '' }}>
                                        <span><strong>Deliver to me</strong><span>Freight calculated below</span></span>
                                    </label>
                                    <label class="gm-radio">
                                        <input type="radio" name="fulfilment" value="pickup" data-fulfilment
                                               {{ old('fulfilment') === 'pickup' ? 'checked' : '' }}>
                                        <span><strong>I'll collect</strong><span>Free &middot; Te Awamutu</span></span>
                                    </label>
                                </div>
                            </fieldset>

                            <fieldset class="gm-fieldset" data-delivery-fields>
                                <legend>Delivery address</legend>
                                <div class="form-group">
                                    <label for="co-a1">Street address *</label>
                                    <input id="co-a1" name="address_line1" type="text" class="form-control pill" value="{{ old('address_line1') }}">
                                </div>
                                <div class="form-group">
                                    <label for="co-a2">Address line 2</label>
                                    <input id="co-a2" name="address_line2" type="text" class="form-control pill" value="{{ old('address_line2') }}">
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="co-suburb">Suburb</label>
                                        <input id="co-suburb" name="suburb" type="text" class="form-control pill" value="{{ old('suburb') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="co-city">Town / city *</label>
                                        <input id="co-city" name="city" type="text" class="form-control pill" value="{{ old('city') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="co-postcode">Postcode</label>
                                        <input id="co-postcode" name="postcode" type="text" class="form-control pill" value="{{ old('postcode') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="co-region">Island *</label>
                                        <select id="co-region" name="region" class="form-control pill" data-region>
                                            @foreach($regions as $value => $label)
                                                <option value="{{ $value }}" {{ old('region') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:400;">
                                        <input type="checkbox" name="is_rural" value="1" data-rural {{ old('is_rural') ? 'checked' : '' }}>
                                        This is a rural delivery address
                                        (+${{ number_format((float) config('shipping.rural_surcharge'), 2) }})
                                    </label>
                                </div>
                            </fieldset>

                            <div class="form-group">
                                <label for="co-notes">Anything we should know?</label>
                                <textarea id="co-notes" name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="gm-shop-card">
                            <h3 style="font-size:1.15rem;margin-bottom:.75rem;">Your order</h3>
                            @foreach($items as $part)
                                <div class="gm-line">
                                    <div class="gm-line-body">
                                        <p class="gm-line-title">{{ $part->title }}</p>
                                        <p class="gm-line-meta">{{ $part->shipping_band?->shortLabel() }}</p>
                                    </div>
                                    <div class="gm-line-price">${{ number_format((float) $part->price, 2) }}</div>
                                </div>
                            @endforeach

                            <div class="gm-totals">
                                <div class="gm-total-row">
                                    <span>Subtotal</span>
                                    <strong>${{ number_format($subtotal, 2) }}</strong>
                                </div>
                                <div class="gm-total-row">
                                    <span>Freight</span>
                                    <strong data-shipping-out>$0.00</strong>
                                </div>
                                <div class="gm-total-row gm-total-row--grand">
                                    <span>Total</span>
                                    <span data-total-out>${{ number_format($subtotal, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="th-btn w-100 mt-3">
                                Pay Securely <i class="fas fa-arrow-up-right"></i>
                            </button>

                            <div class="gm-secure">
                                <i class="fa-solid fa-lock"></i>
                                <span>Payment is handled by Stripe. Your card details never touch our website.</span>
                            </div>

                            <p class="gm-muted mt-3 mb-0">
                                By paying you agree to our
                                <a href="/returns-policy">Returns Policy</a> and
                                <a href="/shipping-policy">Shipping Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Live freight preview. The server recalculates from the same table at
    // submit time — this is display only and is never trusted for the charge.
    (function () {
        var rates = @json(config('shipping.rates'));
        var rural = {{ (float) config('shipping.rural_surcharge', 0) }};
        var band  = @json(optional($band)->value);
        var subtotal = {{ (float) $subtotal }};

        var form = document.getElementById('checkout-form');
        if (!form) return;

        var deliveryFields = form.querySelector('[data-delivery-fields]');
        var regionSelect   = form.querySelector('[data-region]');
        var ruralBox       = form.querySelector('[data-rural]');
        var shipOut        = form.querySelector('[data-shipping-out]');
        var totalOut       = form.querySelector('[data-total-out]');

        function money(n) { return '$' + n.toFixed(2); }

        function update() {
            var pickup = form.querySelector('[data-fulfilment]:checked').value === 'pickup';
            deliveryFields.style.display = pickup ? 'none' : '';

            var ship = 0;
            if (!pickup && band && rates[band]) {
                ship = parseFloat(rates[band][regionSelect.value] || 0);
                if (ruralBox.checked) ship += rural;
            }

            shipOut.textContent = money(ship);
            totalOut.textContent = money(subtotal + ship);
        }

        form.querySelectorAll('[data-fulfilment]').forEach(function (el) {
            el.addEventListener('change', update);
        });
        regionSelect.addEventListener('change', update);
        ruralBox.addEventListener('change', update);
        update();
    })();
</script>
@endpush
