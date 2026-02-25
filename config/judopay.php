<?php

// config/judopay.php

$isSandbox = env('JUDOPAY_SANDBOX', true);

return [

    // Credentials
    'token' => $isSandbox ? env('JUDOPAY_TOKEN') : env('JUDOPAY_TOKEN_LIVE'),
    'secret' => $isSandbox ? env('JUDOPAY_SECRET') : env('JUDOPAY_SECRET_LIVE'),
    'judo_id' => $isSandbox ? env('JUDOPAY_JUDO_ID') : env('JUDOPAY_JUDO_ID_LIVE'),

    // API setup
    'api_version' => env('JUDOPAY_API_VERSION', '6.23'),
    'sandbox' => $isSandbox,
    'base_url' => $isSandbox
        ? env('JUDOPAY_BASE_URL', 'https://api-sandbox.judopay.com')
        : env('JUDOPAY_BASE_URL_LIVE', 'https://api.judopay.com'),

    // Payment defaults
    'currency' => env('JUDOPAY_CURRENCY', 'GBP'),
    'country_code' => 826, // UK ISO code

    // Timeouts & retries
    'timeout' => env('JUDOPAY_TIMEOUT', 30),
    'retry_attempts' => env('JUDOPAY_RETRY_ATTEMPTS', 1),

    // Headers
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ],

    // Webhooks
    'webhook' => [
        'username' => $isSandbox ? env('JUDOPAY_WEBHOOK_USERNAME') : env('JUDOPAY_WEBHOOK_USERNAME_LIVE'),
        'password' => $isSandbox ? env('JUDOPAY_WEBHOOK_PASSWORD') : env('JUDOPAY_WEBHOOK_PASSWORD_LIVE'),
    ],

    // Endpoints
    'endpoints' => [
        'webpayments' => '/webpayments/payments', // CIT
        'transactions' => '/transactions/payments', // MIT
        'enquiry_webpayment' => '/webpayments/{reference}',
        'enquiry_transaction' => '/transactions/{receiptId}',
        'refunds' => '/transactions/refunds',
    ],

    // 3DS2 Challenge Request Indicator options (Judopay official set)
    'threeDS2Options' => [
        'noPreference' => [
            'meaning' => 'Leave it up to issuer',
            'behaviour' => 'Issuer decides whether to challenge',
        ],
        'noChallenge' => [
            'meaning' => 'Request frictionless flow',
            'behaviour' => 'Ask issuer not to challenge',
        ],
        'challengePreferred' => [
            'meaning' => 'Prefer challenge if possible',
            'behaviour' => 'Suggest stronger authentication',
        ],
        'challengeAsMandate' => [
            'meaning' => 'Force challenge',
            'behaviour' => 'Require user authentication (e.g., via bank app)',
        ],
    ],

    // Customer Initiated Transactions
    'cit' => [
        'reference_prefix' => 'CIT',
        'success_url' => env('APP_URL').'/judopay/success/LnPqbMwqAXvCU',
        'cancel_url' => env('APP_URL').'/judopay/failure/enpWqTqAU',
        'refund_mode' => env('JUDOPAY_CIT_REFUND_MODE', 'automatic'), // 'automatic' or 'manual'
        'refund_amount_behaviour' => env('JUDOPAY_CIT_REFUND_AMOUNT_BEHAVIOUR', 'full'), // 'full' or 'custom'
    ],

    // Merchant Initiated Transactions
    'mit' => [
        'reference_prefix' => 'MIT',
        'enable_automatic_retry' => env('JUDOPAY_ENABLE_MIT_RETRY', false), // CENTRAL RETRY CONTROL
        'max_retry_attempts' => 2,
        'retry_delay_hours' => 12,
        'dispatch_delay_seconds' => 5, // Delay between individual job dispatches

        // Queue PRODUCE job timing (when to generate MIT queue records)
        'queue_produce_time' => '04:00', // When to run ProduceNgnMitQueueJob
        'queue_produce_frequency' => env('JUDOPAY_MIT_QUEUE_PRODUCE_FREQUENCY', 'weekly'),

        // Queue EXEC job timing (when individual MIT payments fire)
        'queue_exec_time' => env('JUDOPAY_MIT_QUEUE_EXEC_TIME', '00:05:00'), // When to fire MIT on due date

        // RETRY SYSTEM for payment declines (not API errors)
        'retry_system' => [
            'enabled' => env('JUDOPAY_MIT_RETRY_SYSTEM_ENABLED', true),
            'retry_time' => env('JUDOPAY_MIT_RETRY_TIME', '16:45:00'), // 4:45pm daily retry
        ],

        // AUTOMATION SETTINGS
        'auto_add_to_queue_time' => '08:30', // When to auto-add NGN MIT Queue to Judopay MIT Queue
        'automation_user_id' => 93, // System user for automated actions
    ],

    // eg NGNR-131-220, NGNI-3-310
    'consumer_reference_format' => [
        'App\Models\RentingBooking' => 'NGNR-{rental_id}-{customer_id}',
        'App\Models\FinanceApplication' => 'NGNI-{contract_id}-{customer_id}',
    ],

    // Reference formatting
    'reference_format' => [
        'cit' => '{prefix}-{consumer_reference}-{timestamp}',
        'mit' => '{prefix}-{consumer_reference}-{timestamp}',
        'refund' => 'REFUND-{consumer_reference}-{timestamp}',
    ],

    'bank_response_codes' => [
        // Single digit codes (common in sandbox/testing)
        '0' => 'SUCCESS',
        '1' => 'DECLINED',
        '2' => 'DECLINED',
        '3' => 'DECLINED',
        '4' => 'DECLINED',
        '5' => 'DO_NOT_HONOR', // ISO 8583 standard - general decline (JudoPay sandbox may use for insufficient funds)
        '6' => 'SYSTEM_ERROR',
        '7' => 'DECLINED',
        '8' => 'SUCCESS',
        '9' => 'RETRY',

        // Standard two-digit codes
        '00' => 'SUCCESS',
        '01' => 'DECLINED',
        '02' => 'DECLINED',
        '03' => 'DECLINED',
        '04' => 'DECLINED',
        '05' => 'DECLINED',
        '06' => 'SYSTEM_ERROR',
        '07' => 'DECLINED',
        '08' => 'SUCCESS',
        '09' => 'RETRY',
        '10' => 'SUCCESS',
        '11' => 'SUCCESS',
        '12' => 'INVALID_CARD',
        '13' => 'INVALID_AMOUNT',
        '14' => 'INVALID_CARD',
        '15' => 'INVALID_CARD',
        '17' => 'CANCELLATION',
        '19' => 'RETRY',
        '28' => 'RETRY',
        '30' => 'SYSTEM_ERROR',
        '33' => 'EXPIRED_CARD',
        '34' => 'SUSPECTED_FRAUD',
        '36' => 'DECLINED',
        '38' => 'DECLINED',
        '41' => 'DECLINED',
        '43' => 'DECLINED',
        '51' => 'INSUFFICIENT_FUNDS', // Official ISO 8583 code for insufficient funds
        '54' => 'EXPIRED_CARD',
        '55' => 'DECLINED',
        '57' => 'CARD_NOT_ALLOWED',
        '58' => 'CARD_NOT_ALLOWED',
        '59' => 'SUSPECTED_FRAUD',
        '61' => 'DECLINED',
        '62' => 'DECLINED',
        '63' => 'DECLINED',
        '65' => 'DECLINED',
        '68' => 'TIMEOUT',
        '75' => 'DECLINED',
        '78' => 'DECLINED',
        '80' => 'RETRY',
        '91' => 'NETWORK_ERROR',
        '92' => 'NETWORK_ERROR',
        '93' => 'DECLINED',
        '94' => 'DUPLICATE',
        '95' => 'SYSTEM_ERROR',
        '96' => 'SYSTEM_ERROR',
    ],

    // Consent form version management
    'consent' => [
        // Current version for new customers (change this when releasing new version)
        'current_version' => 'v1.0-judopay-cit',

        // All consent form versions (config-driven, not database)
        'versions' => [
            'v1.0-judopay-cit' => [
                'blade_file' => 'judopay-authorisation-concent-form-v1',
                'effective_date' => '2025-10-07',
                'hash' => '1675f63af7f2a8109486e501ffaf3235a0c61127d78277a4e76304370ada1710',
                'description' => 'Initial Judopay recurring payment consent',
            ],

            // Example for future versions:
            // 'v2.0-judopay-cit' => [
            //     'blade_file' => 'judopay-authorisation-concent-form-v2',
            //     'effective_date' => '2026-01-01',
            //     'hash' => 'generated_hash_here',
            //     'description' => 'Updated with new FCA requirements',
            // ],
        ],
    ],

];
