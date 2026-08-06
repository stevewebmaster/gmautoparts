@extends('miniapp.layout')
@section('title', 'Manage stock')

@push('styles')
<style>
    .stock-search { display: flex; gap: 0.5rem; margin-bottom: 0.9rem; }
    .stock-search input { flex: 1 1 auto; }
    .stock-search button { flex: 0 0 auto; width: auto; margin-bottom: 0; padding: 0.72rem 1.1rem; }
    .stock-filters { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1rem; }
    .stock-chip { padding: 0.4rem 0.8rem; border-radius: 999px; border: 1px solid #d1d5db; background: #fff; color: #374151; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
    .stock-chip.is-active { background: #0f3460; border-color: #0f3460; color: #fff; }
    .stock-item { display: flex; gap: 0.75rem; padding: 0.85rem 0; border-top: 1px solid #e5e7eb; }
    .stock-item:first-of-type { border-top: none; }
    .stock-thumb { flex: 0 0 auto; width: 58px; height: 58px; border-radius: 10px; object-fit: cover; background: #f3f4f6; }
    .stock-body { flex: 1 1 auto; min-width: 0; }
    .stock-title { margin: 0 0 0.15rem; font-size: 1rem; font-weight: 700; color: #0f172a; line-height: 1.3; }
    .stock-meta { margin: 0; font-size: 0.82rem; color: #6b7280; }
    .stock-status { display: inline-block; margin-top: 0.35rem; padding: 0.15em 0.6em; border-radius: 999px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .stock-status--available { background: #d1fae5; color: #065f46; }
    .stock-status--on_hold   { background: #fef3c7; color: #92400e; }
    .stock-status--sold      { background: #fee2e2; color: #991b1b; }
    .stock-status--withdrawn { background: #e5e7eb; color: #374151; }
    .stock-actions { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.55rem; }
    .stock-actions form { margin: 0; }
    .btn-stock { padding: 0.55rem 0.9rem; font-size: 0.88rem; font-weight: 600; border-radius: 10px; border: 1px solid #d1d5db; background: #fff; color: #1f2937; cursor: pointer; }
    .btn-stock:active { transform: scale(0.98); }
    .btn-stock--sold { background: #b91c1c; border-color: #b91c1c; color: #fff; }
    .btn-stock--back { background: #047857; border-color: #047857; color: #fff; }
    .stock-empty { color: #6b7280; text-align: center; padding: 1.5rem 0; margin: 0; }
    .stock-pagination { margin-top: 1rem; }
    .stock-pagination svg { width: 18px; height: 18px; }
</style>
@endpush

@section('content')
    <div class="card-app">
        <h1 class="page-title">Manage Stock</h1>
        <p class="lead-text">Mark parts sold as they leave the yard. Sold parts stay on the website but drop out of the parts listing.</p>

        <form class="stock-search" method="get" action="{{ route('app.stock') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search title, stock #, make, model" inputmode="search">
            @if($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <button type="submit" class="btn-app btn-primary-app">Search</button>
        </form>

        <div class="stock-filters">
            <a href="{{ route('app.stock', array_filter(['q' => $search])) }}"
               class="stock-chip {{ $status === '' ? 'is-active' : '' }}">All</a>
            @foreach(\App\Enums\PartStatus::cases() as $case)
                <a href="{{ route('app.stock', array_filter(['q' => $search, 'status' => $case->value])) }}"
                   class="stock-chip {{ $status === $case->value ? 'is-active' : '' }}">{{ $case->label() }}</a>
            @endforeach
        </div>
    </div>

    <div class="card-app">
        @forelse($parts as $part)
            <div class="stock-item">
                @if(is_array($part->images) && count($part->images))
                    <img class="stock-thumb" src="{{ \Illuminate\Support\Facades\Storage::url($part->images[0]) }}" alt="">
                @else
                    <div class="stock-thumb"></div>
                @endif
                <div class="stock-body">
                    <p class="stock-title">{{ $part->title }}</p>
                    <p class="stock-meta">
                        {{ collect([$part->stock_number ? '#' . $part->stock_number : null, $part->year, $part->make, $part->model])->filter()->implode(' · ') ?: 'No stock details' }}
                    </p>
                    <span class="stock-status stock-status--{{ $part->status->value }}">{{ $part->status->label() }}</span>

                    <div class="stock-actions">
                        @if($part->status !== \App\Enums\PartStatus::Sold)
                            <form method="post" action="{{ route('app.stock.status', $part) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ \App\Enums\PartStatus::Sold->value }}">
                                <button type="submit" class="btn-stock btn-stock--sold">Mark Sold</button>
                            </form>
                        @endif
                        @if($part->status !== \App\Enums\PartStatus::Available)
                            <form method="post" action="{{ route('app.stock.status', $part) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ \App\Enums\PartStatus::Available->value }}">
                                <button type="submit" class="btn-stock btn-stock--back">Available</button>
                            </form>
                        @endif
                        @if($part->status !== \App\Enums\PartStatus::OnHold)
                            <form method="post" action="{{ route('app.stock.status', $part) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ \App\Enums\PartStatus::OnHold->value }}">
                                <button type="submit" class="btn-stock">On Hold</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="stock-empty">
                @if($search !== '' || $status !== '')
                    No parts match that search.
                @else
                    No parts yet. Add one from the dashboard.
                @endif
            </p>
        @endforelse

        @if($parts->hasPages())
            <div class="stock-pagination">{{ $parts->links() }}</div>
        @endif
    </div>

    <p style="margin-top: 1rem;">
        <a href="{{ route('app.dashboard') }}" class="back-link">&larr; Back to Quick Actions</a>
    </p>
@endsection
