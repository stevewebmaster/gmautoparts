<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google reCAPTCHA v3: asks Google how likely it is that a form was sent by a
 * person, and turns it away if the answer is "probably a bot".
 *
 * Fails closed when the token is missing, stale, minted for a different form,
 * or scores low. Fails open when the check itself is broken (Google
 * unreachable, or our secret key rejected), so a Google outage or a typo in
 * .env cannot stop customers reserving parts or checking out. The route
 * throttles still apply either way.
 */
class Recaptcha
{
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function enabled(): bool
    {
        return filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public function passes(?string $token, string $action, ?string $ip): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (blank($token)) {
            Log::info('reCAPTCHA rejected: no token', ['action' => $action, 'ip' => $ip]);

            return false;
        }

        try {
            $result = Http::asForm()->timeout(5)->post(self::VERIFY_URL, [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ])->throw()->json();
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }

        if (! is_array($result)) {
            Log::warning('reCAPTCHA check failed, letting the form through', [
                'action' => $action,
                'reason' => is_string($result) ? $result : 'Response was not JSON',
            ]);

            return true;
        }

        $errors = $result['error-codes'] ?? [];

        if (array_intersect($errors, ['missing-input-secret', 'invalid-input-secret'])) {
            Log::error('reCAPTCHA: Google rejected RECAPTCHA_SECRET_KEY, letting the form through', [
                'action' => $action,
                'errors' => $errors,
            ]);

            return true;
        }

        $passed = ($result['success'] ?? false) === true
            && ($result['action'] ?? null) === $action
            && (float) ($result['score'] ?? 0) >= config('services.recaptcha.min_score');

        if (! $passed) {
            Log::info('reCAPTCHA rejected', [
                'action' => $action,
                'token_action' => $result['action'] ?? null,
                'score' => $result['score'] ?? null,
                'errors' => $errors,
                'ip' => $ip,
            ]);
        }

        return $passed;
    }
}
