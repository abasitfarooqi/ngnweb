<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SiteLaunchGate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('launch.public_live', true)) {
            return $next($request);
        }

        if ($this->isExcludedPath($request)) {
            return $next($request);
        }

        if ($this->hasPreviewAccess($request)) {
            return $next($request);
        }

        if (config('launch.mode') === 'redirect') {
            return redirect()->away($this->liveLegacyUrl(), 302);
        }

        return redirect('/under-construction', 302);
    }

    protected function cookieSecure(Request $request): bool
    {
        if ($request->isSecure()) {
            return true;
        }

        return (bool) config('session.secure', false);
    }

    protected function isExcludedPath(Request $request): bool
    {
        $path = trim($request->path(), '/');

        foreach (config('launch.except_prefixes', []) as $prefix) {
            $prefix = trim((string) $prefix, '/');
            if ($prefix === '') {
                continue;
            }
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    protected function hasPreviewAccess(Request $request): bool
    {
        if ($this->ipIsAllowed($request)) {
            return true;
        }

        $secret = (string) config('launch.preview_secret', '');
        if ($secret === '') {
            return false;
        }

        $queryToken = (string) $request->query('site_preview', '');
        if ($queryToken !== '' && hash_equals($secret, $queryToken)) {
            Cookie::queue(
                $this->previewCookieName(),
                $this->signedPreviewValue($secret),
                $this->previewCookieMinutes(),
                '/',
                null,
                $this->cookieSecure($request),
                true,
                false,
                'lax'
            );

            return true;
        }

        $cookie = (string) $request->cookie($this->previewCookieName(), '');

        return $cookie !== '' && hash_equals($this->signedPreviewValue($secret), $cookie);
    }

    protected function ipIsAllowed(Request $request): bool
    {
        $allowed = config('launch.preview_ips', []);
        if (! is_array($allowed) || $allowed === []) {
            return false;
        }

        $ip = (string) $request->ip();

        return in_array($ip, $allowed, true);
    }

    protected function previewCookieName(): string
    {
        return (string) config('launch.preview_cookie', 'ngn_launch_preview');
    }

    protected function previewCookieMinutes(): int
    {
        $days = (int) config('launch.preview_cookie_days', 30);

        return max(1, $days) * 24 * 60;
    }

    protected function signedPreviewValue(string $secret): string
    {
        return hash_hmac('sha256', $secret, (string) config('app.key'));
    }

    protected function liveLegacyUrl(): string
    {
        return (string) config('launch.live_legacy_url', 'https://neguinhomotors.co.uk');
    }
}
