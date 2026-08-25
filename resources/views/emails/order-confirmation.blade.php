<x-mail::message>
# Thanks {{ $order->name }} — your order is confirmed

**Order:** {{ $order->reference }}
**Paid:** ${{ number_format((float) $order->total, 2) }} NZD

<x-mail::table>
| Part | Price |
| :--- | ----: |
@foreach($order->items as $item)
| {{ $item->title }} | ${{ number_format((float) $item->price, 2) }} |
@endforeach
| **Subtotal** | **${{ number_format((float) $order->subtotal, 2) }}** |
| {{ $order->isPickup() ? 'Collection' : 'Freight' }} | ${{ number_format((float) $order->shipping, 2) }} |
| **Total paid** | **${{ number_format((float) $order->total, 2) }}** |
</x-mail::table>

@if($order->isPickup())
## Collecting
Your parts are set aside at our Te Awamutu yard. Bring your order number
**{{ $order->reference }}** with you. There is nothing further to pay.
@else
## Delivery
We will pack and dispatch your order, and email you when it is on its way.

{{ $order->address_line1 }}@if($order->address_line2), {{ $order->address_line2 }}@endif<br>
@if($order->suburb){{ $order->suburb }}<br>@endif
{{ $order->city }} {{ $order->postcode }}@if($order->is_rural) (rural)@endif
@endif

@if($order->notes)
**Your notes:** {{ $order->notes }}
@endif

<x-mail::button :url="$url">
View your order
</x-mail::button>

Questions? Reply to this email or call **07 849 8814**.
See our [Returns Policy]({{ url('/returns-policy') }}) and [Shipping Policy]({{ url('/shipping-policy') }}).

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
