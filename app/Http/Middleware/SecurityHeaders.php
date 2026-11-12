<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser security headers on every page Laravel serves. Images, CSS and JS
 * come straight from nginx and do not get them, which is fine: these headers
 * protect pages, not files.
 *
 * Deliberately left out:
 * - A full Content-Security-Policy. The site's templates, reCAPTCHA, the map
 *   and the admin all rely on inline scripts, so a strict one would break
 *   them. The policy below only covers what is safe everywhere.
 * - CSP `form-action`. Browsers apply it to the redirect after a form posts,
 *   and checkout posts here and then redirects to Stripe.
 */
class SecurityHeaders
{
    protected const HEADERS = [
        // No other site may show these pages inside a frame (clickjacking).
        'X-Frame-Options' => 'SAMEORIGIN',
        'Content-Security-Policy' => "frame-ancestors 'self'; base-uri 'self'; object-src 'none'",
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(self), microphone=(), geolocation=(), payment=()',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        // Tells browsers to use https for this site for a year, even if someone
        // types http:// or follows an old link. Only for a site that is on https:
        // a browser that has seen it will refuse plain http. Subdomains are
        // left out, as there may be ones (mail, old services) without a certificate.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
