<?php

namespace App\Http\Middleware;

use App\Services\Recaptcha;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turns away form posts that reCAPTCHA v3 scores as a bot.
 *
 * Use as ->middleware('recaptcha:contact'). The action must match the one the
 * form passes to the partials.recaptcha view, so a token minted on one form
 * cannot be replayed on another. Does nothing until both reCAPTCHA keys are
 * set — see config/services.php.
 */
class VerifyRecaptcha
{
    public function __construct(protected Recaptcha $recaptcha)
    {
    }

    public function handle(Request $request, Closure $next, string $action): Response
    {
        $token = $request->input('g-recaptcha-response');

        if ($this->recaptcha->passes(is_string($token) ? $token : null, $action, $request->ip())) {
            return $next($request);
        }

        return back()->withErrors([
            'recaptcha_' . $action => 'Sorry, our spam check stopped that from sending. Please try again, or call us on 07 849 8814.',
        ])->withInput($request->except('g-recaptcha-response'));
    }
}
