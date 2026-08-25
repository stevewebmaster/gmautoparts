<?php

/*
 | Third-party service credentials.
 |
 | This file did not exist — the config directory is trimmed — so it is created
 | here for Stripe. Without it config('services.stripe.*') resolves to null and
 | checkout would fail with an unhelpful error.
 */
return [
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),

        /*
         | From the Stripe dashboard when you create the webhook endpoint. The
         | webhook is what actually marks an order paid, so this must be set in
         | production or paid orders will sit at "awaiting payment".
         */
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
];
