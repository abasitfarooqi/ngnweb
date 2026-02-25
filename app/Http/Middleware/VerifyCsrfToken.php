<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/paypal/hook-52dA1x9qX3',
        'judopay/*', // All JudoPay external endpoints
        'twilio/sms/status-callback',
    ];
}
