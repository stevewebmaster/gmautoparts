<?php

/*
 | Freight rates, in NZD, by size band and destination region.
 |
 | A wrecker cannot compute freight from weight in any reliable way — nobody is
 | going to weigh every alternator as it comes off a car — so parts are banded
 | by size when they are loaded, and each band has a flat rate per region.
 |
 | Anything too big or awkward to price this way is banded `quote_only` and is
 | not sold online at all; those parts fall back to reserve-and-collect.
 |
 | Multi-item orders are charged the HIGHEST band in the cart, once, rather than
 | the sum of each item. Several parts normally go in one consignment, so summing
 | would overcharge badly on a three-part order.
 |
 | Rates are deliberately in config, not the database: they change rarely, and a
 | wrong rate is a silent margin leak rather than something anyone would notice.
 | Review them against actual courier invoices.
 */
return [
    'regions' => [
        'north_island' => 'North Island',
        'south_island' => 'South Island',
    ],

    'rates' => [
        'small' => [
            'north_island' => 12.00,
            'south_island' => 18.00,
        ],
        'medium' => [
            'north_island' => 20.00,
            'south_island' => 30.00,
        ],
        'large' => [
            'north_island' => 75.00,
            'south_island' => 110.00,
        ],
    ],

    /*
     | Added on top for rural delivery addresses. Couriers charge a rural
     | surcharge on every consignment regardless of size.
     */
    'rural_surcharge' => 6.50,

    /*
     | Order total (before freight) at or above which freight is free. Set to
     | null to disable. Kept off by default — margins on used parts vary too much
     | for a blanket threshold to be safe without the client's say-so.
     */
    'free_over' => null,
];
