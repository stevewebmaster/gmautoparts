@extends('miniapp.layout')
@section('title', 'Reservations')

@push('styles')
<style>
    .res-filters { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.25rem; }
    .res-chip { padding: 0.4rem 0.8rem; border-radius: 999px; border: 1px solid #d1d5db; background: #fff; color: #374151; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
    .res-chip.is-active { background: #0f3460; border-color: #0f3460; color: #fff; }
    .res-item { padding: 0.9rem 0; border-top: 1px solid #e5e7eb; }
    .res-item:first-of-type { border-top: none; }
    .res-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; }
    .res-ref { font-weight: 800; letter-spacing: 0.05em; color: #0f172a; font-size: 0.95rem; }
    .res-part { margin: 0.25rem 0 0; font-size: 1rem; font-weight: 700; color: #0f172a; line-height: 1.3; }
    .res-meta { margin: 0.2rem 0 0; font-size: 0.85rem; color: #6b7280; }
    .res-meta a { color: #2563eb; text-decoration: none; }
    .res-status { flex: 0 0 auto; padding: 0.15em 0.6em; border-radius: 999px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .res-status--reserved  { background: #fef3c7; color: #92400e; }
    .res-status--collected { background: #d1fae5; color: #065f46; }
    .res-status--cancelled { background: #e5e7eb; color: #374151; }
    .res-status--expired   { background: #fee2e2; color: #991b1b; }
    .res-due { font-weight: 700; }
    .res-due--soon { color: #b91c1c; }
    .res-actions { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.65rem; }
    .res-actions form { margin: 0; }
    .btn-res { padding: 0.55rem 0.9rem; font-size: 0.88rem; font-weight: 600; border-radius: 10px; border: 1px solid #d1d5db; background: #fff; color: #1f2937; cursor: pointer; }
    .btn-res:active { transform: scale(0.98); }
    .btn-res--collected { background: #047857; border-color: #047857; color: #fff; }
    .btn-res--cancel { background: #fff; border-color: #b91c1c; color: #b91c1c; }
    .res-empty { color: #6b7280; text-align: center; padding: 1.5rem 0; margin: 0; }
</style>
@endpush

@section('content')
    <div class="card-app">
        <h1 class="page-title">Reservations</h1>
        <p class="lead-text">
            @if($holdingCount)
                <strong>{{ $holdingCount }}</strong> {{ Str::plural('part', $holdingCount) }} currently held for customers. Mark them collected when they are picked up and paid for.
            @else
                Nothing is being held right now.
            @endif
        </p>

        <div class="res-filters">
            @foreach(\App\Enums\ReservationStatus::cases() as $case)
                <a href="{{ route('app.reservations', ['status' => $case->value]) }}"
                   class="res-chip {{ $status === $case->value ? 'is-active' : '' }}">{{ $case->label() }}</a>
            @endforeach
            <a href="{{ route('app.reservations', ['status' => 'all']) }}"
               class="res-chip {{ ! in_array($status, \App\Enums\ReservationStatus::values(), true) ? 'is-active' : '' }}">All</a>
        </div>
    </div>

    <div class="card-app">
        @forelse($reservations as $reservation)
            <div class="res-item">
                <div class="res-head">
                    <div>
                        <span class="res-ref">{{ $reservation->reference }}</span>
                        <p class="res-part">{{ $reservation->part_title }}</p>
                        <p class="res-meta">
                            {{ $reservation->name }}
                            @if($reservation->phone)
                                &middot; <a href="tel:{{ preg_replace('/\s+/', '', $reservation->phone) }}">{{ $reservation->phone }}</a>
                            @endif
                        </p>
                        <p class="res-meta">
                            <a href="mailto:{{ $reservation->email }}">{{ $reservation->email }}</a>
                            @if($reservation->part_price)
                                &middot; ${{ number_format((float) $reservation->part_price, 2) }}
                            @endif
                        </p>
                        @if($reservation->status === \App\Enums\ReservationStatus::Reserved && $reservation->expires_at)
                            <p class="res-meta">
                                <span class="res-due {{ $reservation->expires_at->isBefore(now()->addDays(2)) ? 'res-due--soon' : '' }}">
                                    Collect by {{ $reservation->expires_at->format('D j M') }}
                                    ({{ $reservation->expires_at->diffForHumans() }})
                                </span>
                            </p>
                        @endif
                        @if($reservation->notes)
                            <p class="res-meta">&ldquo;{{ $reservation->notes }}&rdquo;</p>
                        @endif
                    </div>
                    <span class="res-status res-status--{{ $reservation->status->value }}">{{ $reservation->status->label() }}</span>
                </div>

                @if($reservation->isHolding())
                    <div class="res-actions">
                        <form method="post" action="{{ route('app.reservations.status', $reservation) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ \App\Enums\ReservationStatus::Collected->value }}">
                            <button type="submit" class="btn-res btn-res--collected">Collected &amp; Paid</button>
                        </form>
                        <form method="post" action="{{ route('app.reservations.status', $reservation) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ \App\Enums\ReservationStatus::Cancelled->value }}">
                            <button type="submit" class="btn-res btn-res--cancel">Cancel</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="res-empty">No reservations to show.</p>
        @endforelse

        @if($reservations->hasPages())
            <div style="margin-top: 1rem;">{{ $reservations->links() }}</div>
        @endif
    </div>

    <p style="margin-top: 1rem;">
        <a href="{{ route('app.dashboard') }}" class="back-link">&larr; Back to Quick Actions</a>
    </p>
@endsection
