<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MiniappAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $pin = config('miniapp.pin');

        if (empty($pin)) {
            abort(404, 'Mini-app is not configured.');
        }

        if (session('miniapp_authenticated')) {
            return $next($request);
        }

        if ($request->routeIs('app.login') || $request->routeIs('app.login.post')) {
            return $next($request);
        }

        return redirect()->route('app.login');
    }
}
