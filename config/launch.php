<?php

return [

    /*
    | When true, everyone sees the normal site. When false, visitors are gated.
    */
    'public_live' => (static function (): bool {
        $raw = env('SITE_PUBLIC_LIVE', 'true');
        if ($raw === null || $raw === '') {
            return true;
        }

        return in_array(strtolower((string) $raw), ['1', 'true', 'yes', 'on'], true);
    })(),

    /*
    | redirect = HTTP redirect to live_legacy_url
    | page = show under-construction blade with link to live site
    */
    'mode' => env('SITE_LAUNCH_MODE', 'page'),

    'live_legacy_url' => env('SITE_LAUNCH_REDIRECT_URL', 'https://neguinhomotors.co.uk'),

    /*
    | On under-construction page: seconds before browser goes to live_legacy_url (0 = off).
    */
    'auto_redirect_seconds' => (int) env('SITE_LAUNCH_AUTO_REDIRECT_SECONDS', 0),

    /*
    | Secret token for /site-preview/{token}. Set a long random string in .env on production.
    */
    'preview_secret' => env('SITE_LAUNCH_PREVIEW_SECRET', ''),

    /*
    | Optional comma-separated IPs that always bypass the gate (your home/office IP).
    */
    'preview_ips' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SITE_LAUNCH_PREVIEW_IPS', ''))
    ))),

    'preview_cookie' => 'ngn_launch_preview',

    'preview_cookie_days' => (int) env('SITE_LAUNCH_PREVIEW_COOKIE_DAYS', 30),

    /*
    | URI prefixes that never hit the gate (admin, webhooks, preview unlock).
    */
    'except_prefixes' => [
        'ngn-admin',
        'flux-admin',
        'admin',
        'livewire',
        'api',
        'site-preview',
        'under-construction',
        'build',
        'assets',
        'judopay/webhook',
        'webhook',
        'sanctum',
    ],

];
