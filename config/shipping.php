<?php

/*
 | Freight rates, by freight size and destination region.
 |
 | Every part is given a freight size when it is loaded: one of the same
 | satchels and box sizes the yard picks in NZ Couriers' eDespatchIT when it
 | prints the label (see App\Enums\ShippingBand). Nothing else is guessed — the
 | price online is what that label will cost, so the yard is never out of pocket
 | and the customer is never overcharged.
 |
 | Anything too big or heavy for a courier (bumpers, doors, gearboxes, engines)
 | goes by truck, is sized `quote_only`, and is not sold online at all; those
 | parts fall back to reserve-and-collect.
 |
 | Each part in an order is charged its own freight, because NZ Couriers charge
 | per item even within one consignment.
 |
 | The Google product feed sends these same rates per item, so Google Shopping
 | follows any change here on its next fetch.
 */
return [
    'regions' => [
        'north_island' => 'North Island',
        'south_island' => 'South Island',
    ],

    /*
     | What NZ Couriers charge G&M per box, copied from the "Cost" column in
     | eDespatchIT: BEFORE the variable adjustment and GST below, exactly as the
     | screen shows it. From the Te Awamutu (HLZ) depot:
     |
     |   north_island — Paraparaumu, rated KAP | LH (long haul)
     |   south_island — Christchurch, rated CHC | NZ (inter-island)
     |
     | Long haul is the dearest North Island urban rate, so nowhere on the North
     | Island is under-charged. Rates as at October 2026.
     */
    'courier_rates' => [
        'box_015' => ['north_island' => 12.35, 'south_island' => 27.33],
        'box_025' => ['north_island' => 19.56, 'south_island' => 40.33],
        'box_030' => ['north_island' => 16.77, 'south_island' => 49.46],
        'box_035' => ['north_island' => 19.49, 'south_island' => 57.66],
        'box_040' => ['north_island' => 22.84, 'south_island' => 66.47],
        'box_050' => ['north_island' => 27.63, 'south_island' => 82.18],
        'box_060' => ['north_island' => 33.09, 'south_island' => 98.57],
        'box_075' => ['north_island' => 41.23, 'south_island' => 123.14],
        'box_085' => ['north_island' => 46.65, 'south_island' => 139.50],
        'box_100' => ['north_island' => 54.84, 'south_island' => 164.08],
        'box_125' => ['north_island' => 68.43, 'south_island' => 205.00],
    ],

    /*
     | The percentage eDespatchIT adds on top of every box rate ("Includes
     | agreed variable adjustments of 18.40%"). It moves with fuel prices, so it
     | can be changed in .env without a deploy — then `php artisan config:cache`.
     */
    'variable_adjustment' => (float) env('NZC_VARIABLE_ADJUSTMENT', 18.4),

    'gst' => 15,

    /*
     | NZ Couriers satchels: one flat price to anywhere in the country, BEFORE
     | GST. The fuel charge is already in it, so the variable adjustment is not
     | added — only GST.
     */
    'satchels' => [
        'e20' => 12.00,
        'e60' => 28.00,
    ],

    /*
     | Added for rural delivery addresses, for EACH part — NZ Couriers need a
     | rural ticket on every item. What the customer pays, GST inclusive: book
     | one item to a rural address in eDespatchIT and see how much the total
     | goes up by.
     |
     | PLACEHOLDER until G&M confirm the figure from the booking page.
     */
    'rural_surcharge' => 6.50,

    /*
     | Order total (before freight) at or above which freight is free. Set to
     | null to disable. Kept off by default — margins on used parts vary too much
     | for a blanket threshold to be safe without the client's say-so.
     */
    'free_over' => null,
];
