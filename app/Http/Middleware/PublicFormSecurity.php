<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defensive boundary for public form/API submissions.
 *
 * This does not replace CAPTCHA. It prevents the common case where a bot
 * repeatedly posts the same payload or fills a hidden honeypot field. The
 * route limiter remains responsible for volume control and the controller
 * remains responsible for business validation.
 */
class PublicFormSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $mobilePublicPaths = [
            'api/v1/mobile/mot/check', 'api/v1/mobile/mot/alerts',
            'api/v1/mobile/finance/apply', 'api/v1/mobile/contact/call-back',
            'api/v1/mobile/contact/trade-account', 'api/v1/mobile/contact/service-booking',
            'api/v1/mobile/contact/general', 'api/v1/mobile/enquiries/sales',
            'api/v1/mobile/mot/book', 'api/v1/mobile/recovery/request',
            'api/v1/mobile/newsletter/subscribe', 'api/v1/mobile/surveys/submit',
            'api/v1/mobile/partners/subscribe', 'api/v1/mobile/accident-management/claim',
        ];
        $isMobilePublicPath = in_array($path, $mobilePublicPaths, true)
            || Str::is('api/v1/mobile/rentals/*/enquiry', $path)
            || Str::is('api/v1/mobile/bikes/*/*/enquiry', $path);

        // The middleware is attached to the broad mobile route group so the
        // public endpoints cannot be forgotten. Restrict its checks to the
        // actual public form paths; catalogue, auth, portal and Club writes
        // have their own contracts and must pass through untouched.
        if (str_starts_with($path, 'api/v1/mobile/') && ! $isMobilePublicPath) {
            return $next($request);
        }

        // Authenticated portal/customer and staff actions have their own
        // authorization and should not be treated as public form traffic.
        if ($request->user('customer') || $request->user('sanctum') || $request->user()) {
            return $next($request);
        }

        $honeypot = trim((string) ($request->input('companyWebsite')
            ?? $request->input('website_confirm')
            ?? $request->input('website')));

        if ($honeypot !== '') {
            return $this->blocked($request, 'honeypot');
        }

        $captchaEnabled = (bool) config('contact.captcha_enabled', false);
        $captchaToken = trim((string) ($request->input('captcha_token')
            ?? $request->input('captchaToken')
            ?? $request->header('X-Captcha-Token')));

        if ($captchaEnabled && $captchaToken === '') {
            return $this->blocked($request, 'missing_captcha');
        }

        $fingerprint = hash('sha256', json_encode([
            $request->path(),
            strtolower(trim((string) ($request->input('email') ?? $request->input('customer_email') ?? ''))),
            trim((string) ($request->input('phone') ?? $request->input('customer_phone') ?? '')),
            trim((string) ($request->input('reg_no') ?? $request->input('vrm') ?? $request->input('bikeReg') ?? '')),
            trim((string) ($request->input('service_type') ?? $request->input('subject') ?? '')),
            trim((string) ($request->input('message') ?? $request->input('description') ?? $request->input('note') ?? '')),
        ], JSON_UNESCAPED_UNICODE));

        $seconds = (int) config('contact.api_duplicate_seconds', 180);
        if ($seconds > 0 && ! Cache::add('public-form:duplicate:'.$fingerprint, true, now()->addSeconds($seconds))) {
            return $this->blocked($request, 'duplicate');
        }

        return $next($request);
    }

    private function blocked(Request $request, string $reason): Response
    {
        Log::warning('Public form submission blocked', [
            'reason' => $reason,
            'path' => $request->path(),
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        return response()->json([
            'message' => 'We could not process this submission. Please check your details and try again.',
        ], 422);
    }
}
