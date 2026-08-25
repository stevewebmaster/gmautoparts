<x-mail::message>
# Your part is reserved

Thanks {{ $reservation->name }} — we have put this aside for you.

**Reference:** {{ $reservation->reference }}
**Part:** {{ $reservation->part_title }}
@if($reservation->part_price)
**Price:** ${{ number_format((float) $reservation->part_price, 2) }} (NZD)
@endif
@if($reservation->expires_at)
**Please collect by:** {{ $reservation->expires_at->format('l j F Y') }}
@endif

## Paying

There is nothing to pay now — **you pay when you collect the part**. Bring your
reference number with you.

@if($reservation->expires_at)
If you cannot make it by {{ $reservation->expires_at->format('j F') }}, just reply to this email and
we will do our best to hold it longer. Otherwise the part goes back on sale.
@endif

<x-mail::button :url="$url">
View your reservation
</x-mail::button>

If anything looks wrong, reply to this email or call us on 07 849 8814.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
