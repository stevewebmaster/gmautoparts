<x-mail::message>
# New reservation

**Reference:** {{ $reservation->reference }}
**Part:** {{ $reservation->part_title }}
@if($reservation->part_price)
**Price:** ${{ number_format((float) $reservation->part_price, 2) }} (NZD)
@endif
@if($reservation->expires_at)
**Holding until:** {{ $reservation->expires_at->format('l j F Y') }}
@endif

## Customer

**Name:** {{ $reservation->name }}
**Email:** {{ $reservation->email }}
**Phone:** {{ $reservation->phone ?: 'Not supplied' }}

@if($reservation->notes)
**Notes:**
{{ $reservation->notes }}
@endif

The part has been set to **On hold**, so it has dropped out of the parts listing
and shows as unavailable on Google. Mark it Collected or Cancelled in the
Parts Loader app when you know either way.

<x-mail::button :url="$url">
Manage reservations
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
