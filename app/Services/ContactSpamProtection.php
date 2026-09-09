<?php

namespace App\Services;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ContactSpamProtection
{
    public function check(string $honeypot, int $startedAt, array $validated, ?string $captchaToken = null): void
    {
        if (trim($honeypot) !== '') {
            $this->blocked('honeypot');
        }

        if (now()->timestamp - $startedAt < (int) config('contact.minimum_submit_seconds', 2)) {
            $this->blocked('too_fast');
        }

        if (config('contact.captcha_enabled') === true && (! $captchaToken || ! NoCaptcha::verifyResponse($captchaToken, request()->ip()))) {
            $this->blocked('captcha');
        }

        $limits = config('contact.rate_limit', []);
        $decay = (int) ($limits['decay_seconds'] ?? 600);
        $ipKey = 'contact:ip:'.hash('sha256', (string) request()->ip());
        if (RateLimiter::tooManyAttempts($ipKey, (int) ($limits['ip_attempts'] ?? 5))) {
            $this->blocked('rate_limit_ip');
        }

        $email = $this->email($validated);
        if ($email !== '') {
            $emailKey = 'contact:email:'.hash('sha256', mb_strtolower($email));
            if (RateLimiter::tooManyAttempts($emailKey, (int) ($limits['email_attempts'] ?? 5))) {
                $this->blocked('rate_limit_email');
            }
        }

        RateLimiter::hit($ipKey, $decay);
        if ($email !== '') {
            RateLimiter::hit('contact:email:'.hash('sha256', mb_strtolower($email)), $decay);
        }

        $fingerprint = hash('sha256', json_encode([
            'contact',
            mb_strtolower($email),
            $this->value($validated, ['phone']),
            $this->value($validated, ['topic', 'subject', 'serviceType', 'service_type']),
            $this->value($validated, ['message', 'description', 'notes', 'contactMessage']),
            $this->value($validated, ['registration', 'regNo', 'reg_no']),
        ], JSON_UNESCAPED_UNICODE));

        if (! Cache::add('contact:duplicate:'.$fingerprint, true, now()->addSeconds((int) config('contact.duplicate_seconds', 180)))) {
            $this->blocked('duplicate');
        }
    }

    private function email(array $data): string
    {
        foreach (['email', 'contactEmail'] as $key) {
            if (isset($data[$key]) && trim((string) $data[$key]) !== '') {
                return trim((string) $data[$key]);
            }
        }
        return '';
    }

    private function value(array $data, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return preg_replace('/\s+/', ' ', trim((string) $data[$key]));
            }
        }
        return '';
    }

    private function blocked(string $reason): never
    {
        Log::warning('Public contact enquiry blocked', [
            'reason' => $reason,
            'ip_hash' => hash('sha256', (string) request()->ip()),
        ]);

        throw ValidationException::withMessages([
            'form' => 'We could not process this enquiry. Please check your details and try again.',
        ]);
    }
}
