@extends('miniapp.layout')
@section('title', 'Online orders')

@push('styles')
<style>
    .ord-filters { display:flex; flex-wrap:wrap; gap:.4rem; }
    .ord-chip { padding:.4rem .8rem; border-radius:999px; border:1px solid #d1d5db; background:#fff; color:#374151; font-size:.85rem; font-weight:600; text-decoration:none; }
    .ord-chip.is-active { background:#0f3460; border-color:#0f3460; color:#fff; }
    .ord-item { padding:.9rem 0; border-top:1px solid #e5e7eb; }
    .ord-item:first-of-type { border-top:none; }
    .ord-head { display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; }
    .ord-ref { font-weight:800; letter-spacing:.04em; color:#0f172a; }
    .ord-total { font-weight:800; color:#0f172a; white-space:nowrap; }
    .ord-meta { margin:.2rem 0 0; font-size:.85rem; color:#6b7280; }
    .ord-meta a { color:#2563eb; text-decoration:none; }
    .ord-method { display:inline-block; margin-top:.35rem; padding:.15em .6em; border-radius:999px; font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .ord-method--delivery { background:#dbeafe; color:#1e40af; }
    .ord-method--pickup   { background:#e0e7ff; color:#3730a3; }
    .ord-status { flex:0 0 auto; padding:.15em .6em; border-radius:999px; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    .ord-status--pending    { background:#fef3c7; color:#92400e; }
    .ord-status--paid       { background:#d1fae5; color:#065f46; }
    .ord-status--dispatched { background:#dbeafe; color:#1e40af; }
    .ord-status--collected  { background:#dbeafe; color:#1e40af; }
    .ord-status--cancelled  { background:#e5e7eb; color:#374151; }
    .ord-status--refunded   { background:#fee2e2; color:#991b1b; }
    .ord-parts { margin:.5rem 0 0; padding-left:1.1rem; font-size:.9rem; color:#374151; }
    .ord-addr { margin:.5rem 0 0; padding:.6rem .8rem; background:#f8fafc; border-radius:8px; font-size:.88rem; color:#0f172a; line-height:1.45; }
    .ord-actions { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.65rem; }
    .ord-actions form { margin:0; }
    .btn-ord { padding:.55rem .9rem; font-size:.88rem; font-weight:600; border-radius:10px; border:1px solid #d1d5db; background:#fff; color:#1f2937; cursor:pointer; }
    .btn-ord--go { background:#047857; border-color:#047857; color:#fff; }
    .ord-empty { color:#6b7280; text-align:center; padding:1.5rem 0; margin:0; }
</style>
@endpush

@section('content')
    <div class="card-app">
        <h1 class="page-title">Online Orders</h1>
        <p class="lead-text">
            @if($toPackCount)
                <strong>{{ $toPackCount }}</strong> paid {{ Str::plural('order', $toPackCount) }} to pick and pack.
            @else
                Nothing waiting to be packed.
            @endif
        </p>
        <div class="ord-filters">
            @foreach([\App\Enums\OrderStatus::Paid, \App\Enums\OrderStatus::Dispatched, \App\Enums\OrderStatus::Collected, \App\Enums\OrderStatus::Pending, \App\Enums\OrderStatus::Cancelled] as $case)
                <a href="{{ route('app.orders', ['status' => $case->value]) }}"
                   class="ord-chip {{ $status === $case->value ? 'is-active' : '' }}">{{ $case->label() }}</a>
            @endforeach
            <a href="{{ route('app.orders', ['status' => 'all']) }}"
               class="ord-chip {{ ! in_array($status, \App\Enums\OrderStatus::values(), true) ? 'is-active' : '' }}">All</a>
        </div>
    </div>

    <div class="card-app">
        @forelse($orders as $order)
            <div class="ord-item">
                <div class="ord-head">
                    <div>
                        <span class="ord-ref">{{ $order->reference }}</span>
                        <p class="ord-meta">
                            {{ $order->name }}
                            @if($order->phone)
                                &middot; <a href="tel:{{ preg_replace('/\s+/', '', $order->phone) }}">{{ $order->phone }}</a>
                            @endif
                        </p>
                        <p class="ord-meta"><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></p>
                        <span class="ord-method ord-method--{{ $order->isPickup() ? 'pickup' : 'delivery' }}">
                            {{ $order->isPickup() ? 'Collecting' : 'Deliver' }}
                        </span>
                    </div>
                    <div style="text-align:right;">
                        <span class="ord-status ord-status--{{ $order->status->value }}">{{ $order->status->label() }}</span>
                        <p class="ord-total" style="margin:.35rem 0 0;">${{ number_format((float) $order->total, 2) }}</p>
                    </div>
                </div>

                <ul class="ord-parts">
                    @foreach($order->items as $item)
                        <li>{{ $item->title }} — ${{ number_format((float) $item->price, 2) }}</li>
                    @endforeach
                </ul>
                <p class="ord-meta">
                    Parts ${{ number_format((float) $order->subtotal, 2) }}
                    + freight ${{ number_format((float) $order->shipping, 2) }}@if($order->is_rural) (rural)@endif
                </p>

                @unless($order->isPickup())
                    <div class="ord-addr">
                        {{ $order->address_line1 }}@if($order->address_line2), {{ $order->address_line2 }}@endif<br>
                        @if($order->suburb){{ $order->suburb }}<br>@endif
                        {{ $order->city }} {{ $order->postcode }}@if($order->is_rural) <strong>— RURAL</strong>@endif
                    </div>
                @endunless

                @if($order->notes)
                    <p class="ord-meta">&ldquo;{{ $order->notes }}&rdquo;</p>
                @endif

                @if($order->status === \App\Enums\OrderStatus::Paid)
                    <div class="ord-actions">
                        <form method="post" action="{{ route('app.orders.status', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $order->isPickup() ? \App\Enums\OrderStatus::Collected->value : \App\Enums\OrderStatus::Dispatched->value }}">
                            <button type="submit" class="btn-ord btn-ord--go">
                                {{ $order->isPickup() ? 'Collected' : 'Mark Dispatched' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="ord-empty">No orders to show.</p>
        @endforelse

        @if($orders->hasPages())
            <div style="margin-top:1rem;">{{ $orders->links() }}</div>
        @endif
    </div>

    <p style="margin-top:1rem;">
        <a href="{{ route('app.dashboard') }}" class="back-link">&larr; Back to Quick Actions</a>
    </p>
@endsection
