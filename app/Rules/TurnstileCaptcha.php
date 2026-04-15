<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileCaptcha implements ValidationRule
{
    /**
     * Mark this rule as implicit so it runs even when the attribute is empty.
     */
    public bool $implicit = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('services.turnstile.enabled')) {
            return;
        }

        if (empty($value) || !is_string($value)) {
            $fail('La vérification anti-robot est requise.');
            return;
        }

        try {
            $response = Http::timeout(5)->asForm()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret'   => config('services.turnstile.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Turnstile verification exception', ['message' => $e->getMessage()]);
            $fail('Service anti-robot temporairement indisponible. Merci de réessayer.');
            return;
        }

        if ($response->failed() || !($response->json('success') ?? false)) {
            Log::info('Turnstile rejected', [
                'status' => $response->status(),
                'errors' => $response->json('error-codes'),
            ]);
            $fail('La vérification anti-robot a échoué. Merci de réessayer.');
        }
    }
}
