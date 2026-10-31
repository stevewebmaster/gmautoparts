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

    /*
     | Google reCAPTCHA v3 on the public forms. Off until both keys are set, so
     | local dev and a fresh server work without them. Keys come from
     | https://www.google.com/recaptcha/admin (type: score based, v3).
     |
     | min_score: Google scores each submission from 0.0 (bot) to 1.0 (person).
     | 0.5 is Google's suggested starting point; lower it if genuine customers
     | are being turned away (rejections are logged with their score).
     */
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    ],
];
