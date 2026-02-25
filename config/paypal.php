<?php

/**
 * config/paypal.php
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    // Add these top-level keys for easier access
    'client_id' => env('PAYPAL_MODE') === 'live'
        ? env('PAYPAL_LIVE_CLIENT_ID')
        : env('PAYPAL_SANDBOX_CLIENT_ID'),

    'secret' => env('PAYPAL_MODE') === 'live'
        ? env('PAYPAL_LIVE_CLIENT_SECRET')
        : env('PAYPAL_SANDBOX_CLIENT_SECRET'),

    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', 'AWAOUmz_9RDNGSu88eF7H34zK1Eq94z7bxVw9CH_h3I0pMuIyABnB8RxEh23Z8wEUQFKftyfW7UDv80w'),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', 'EJYjqWn03p36FFJVhP6h-WdRSxz_yyKKSmXulvGrgN9wQ9HvJIu9GBP0dtWHzukidAkhJT7Ucv-yMfaO'),
        'app_id' => env('PAYPAL_SANDBOX_APP_ID', 'NGN_ECOMMERCE_SANDBOX'),
        'webhook_id' => env('PAYPAL_SANDBOX_WEBHOOK_ID', ''),
        'webhook_url' => env('PAYPAL_SANDBOX_WEBHOOK_URL', ''),
    ],

    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
        'webhook_id' => env('PAYPAL_LIVE_WEBHOOK_ID', ''),
        'webhook_url' => env('PAYPAL_LIVE_WEBHOOK_URL', ''),
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
    'currency' => env('PAYPAL_CURRENCY', 'GBP'),
    'notify_url' => env('PAYPAL_NOTIFY_URL', ''),
    'locale' => env('PAYPAL_LOCALE', 'en_GB'),

    'webhook_debug' => env('PAYPAL_WEBHOOK_DEBUG', false),

    'webhook_id' => env('PAYPAL_MODE') === 'live' ? env('PAYPAL_LIVE_WEBHOOK_ID') : env('PAYPAL_SANDBOX_WEBHOOK_ID'),
    'webhook_url' => env('PAYPAL_MODE') === 'live' ? env('PAYPAL_LIVE_WEBHOOK_URL') : env('PAYPAL_SANDBOX_WEBHOOK_URL'),
];
