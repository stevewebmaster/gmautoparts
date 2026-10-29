<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * One address for the whole site: https on APP_URL's host.
 *
 * The container answers on www, the old staging host and plain http as well,
 * which Google treats as four copies of every page. Requests to a host listed
 * in REDIRECT_HOSTS, or arriving over plain http, get a permanent redirect.
 *
 * Plain http is only recognised from the proxy's X-Forwarded-Proto saying so.
 * SiteHost terminates SSL at its proxy, so the app itself always sees http —
 * redirecting on that would loop forever. If the header is missing, nothing is
 * redirected for scheme, which is no worse than before.
 *
 * Only GET and HEAD are redirected: a 301 turns a POST into a GET, so a form
 * left open on an old host still submits rather than silently losing its data.
 */
class RedirectToCanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $aliases = config('app.redirect_hosts');

        if (empty($aliases) || ! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $canonical = rtrim((string) config('app.url'), '/');
        $host = strtolower($request->getHost());

        // Neither check can point at where the request already is, so a
        // mistake in .env cannot put the site into a redirect loop.
        $isAlias = $host !== strtolower((string) parse_url($canonical, PHP_URL_HOST))
            && in_array($host, $aliases, true);

        // The first value is the client-facing hop if proxies are chained.
        $isPlainHttp = str_starts_with($canonical, 'https://')
            && strtolower(trim(explode(',', (string) $request->header('X-Forwarded-Proto'))[0])) === 'http';

        if (! $isAlias && ! $isPlainHttp) {
            return $next($request);
        }

        return redirect()->to($canonical . $request->getRequestUri(), 301);
    }
}
