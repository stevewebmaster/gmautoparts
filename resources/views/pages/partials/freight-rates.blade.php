{{-- The freight table on the shipping policy, from config/shipping.php: the
     same rates checkout and the Google feed charge. --}}
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Sent in</th>
                @foreach(config('shipping.regions') as $region)
                    <th class="text-end">{{ $region }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Enums\ShippingBand::cases() as $size)
                @continue(! $size->isShippable())
                <tr>
                    <td>{{ $size->label() }}</td>
                    @foreach($size->rates() as $rate)
                        <td class="text-end">${{ number_format($rate) }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr>
                <td>Bumpers, doors, panels, gearboxes, engines</td>
                <td colspan="{{ count(config('shipping.regions')) }}" class="text-end">By truck &mdash; quoted before you pay</td>
            </tr>
        </tbody>
    </table>
</div>
<p>
    Freight is per part and includes GST. Rural addresses add
    ${{ number_format((float) config('shipping.rural_surcharge'), 2) }} per part.
    @if(config('shipping.free_over') !== null)
        Freight is free on orders of ${{ number_format((float) config('shipping.free_over')) }} or more.
    @endif
    As a guide, a 0.03 m³ box is about 31&nbsp;cm each way, and a 0.125 m³ box about 50&nbsp;cm.
</p>
