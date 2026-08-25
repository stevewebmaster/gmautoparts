<x-mail::message>
# Paid order — pick and pack

**Order:** {{ $order->reference }}
**Paid:** ${{ number_format((float) $order->total, 2) }} NZD
**Method:** {{ $order->isPickup() ? 'CUSTOMER COLLECTING' : 'DELIVERY' }}

<x-mail::table>
| Part | Band | Price |
| :--- | :--- | ----: |
@foreach($order->items as $item)
| {{ $item->title }} | {{ $item->shipping_band?->shortLabel() ?? '—' }} | ${{ number_format((float) $item->price, 2) }} |
@endforeach
</x-mail::table>

Freight charged: **${{ number_format((float) $order->shipping, 2) }}**@if($order->is_rural) (includes rural surcharge)@endif

## Customer

**Name:** {{ $order->name }}
**Email:** {{ $order->email }}
**Phone:** {{ $order->phone ?: 'Not supplied' }}

@if(! $order->isPickup())
**Deliver to:**
{{ $order->address_line1 }}@if($order->address_line2), {{ $order->address_line2 }}@endif<br>
@if($order->suburb){{ $order->suburb }}<br>@endif
{{ $order->city }} {{ $order->postcode }}{!! $order->is_rural ? ' — <strong>RURAL</strong>' : '' !!}
@endif

@if($order->notes)
**Notes:** {{ $order->notes }}
@endif

All parts on this order have been marked **Sold**.

<x-mail::button :url="$url">
Manage orders
</x-mail::button>
</x-mail::message>
