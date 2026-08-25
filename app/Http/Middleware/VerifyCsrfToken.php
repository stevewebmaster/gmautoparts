<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Stripe posts here server-to-server, so there is no session or CSRF token.
     * Authenticity is verified by the webhook signature instead — see
     * StripeCheckout::verifyWebhook().
     */
    protected $except = [
        'stripe/webhook',
    ];
}
