<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transactional Communication System
    |--------------------------------------------------------------------------
    |
    | This system is a control layer for transactional customer communications.
    | Campaign, newsletter, referral, and survey campaign email must not be
    | registered here.
    |
    | Precedence:
    | 1. emergency_bypass=true forces legacy behaviour.
    | 2. system_settings.communication_system_enabled controls the admin switch.
    | 3. per-communication policies apply only when the system is enabled.
    |
    */

    'emergency_bypass' => env('COMMUNICATION_SYSTEM_BYPASS', false),

    'admin_enabled_setting_key' => 'communication_system_enabled',

    'default_enabled' => false,

    'webhook_token' => env('COMMUNICATION_WEBHOOK_TOKEN'),

    'internal_email_domains' => [
        'neguinhomotors.co.uk',
        'ngnmotors.co.uk',
    ],

    'internal_email_addresses' => [
        'admin@neguinhomotors.co.uk',
        'customerservice@neguinhomotors.co.uk',
        'enquiries@neguinhomotors.co.uk',
        'info@neguinhomotors.co.uk',
        'support@neguinhomotors.co.uk',
        'thiago@neguinhomotors.co.uk',
    ],

    'excluded_inventory' => [
        [
            'name' => 'Judopay payment and consent emails',
            'type' => 'EXCLUDED - JUDOPAY SYSTEM',
            'reason' => 'Judopay is explicitly outside this communication system and must remain untouched.',
        ],
        [
            'name' => 'Campaign, survey, referral, newsletter, festive opening-hour and bulk marketing emails',
            'type' => 'EXCLUDED - CAMPAIGN SYSTEM',
            'reason' => 'Marketing/campaign infrastructure is not transactional customer communication control.',
        ],
        [
            'name' => 'Password reset, email verification, passkey reset, raw portal credentials',
            'type' => 'FRAMEWORK/SYSTEM AUTH - REVIEW SEPARATELY',
            'reason' => 'Auth/security emails remain legacy until separately reviewed.',
        ],
        [
            'name' => 'Staff reports, cron summaries, internal admin alerts, support conversation alerts',
            'type' => 'INTERNAL/STAFF - REVIEW SEPARATELY',
            'reason' => 'This panel controls transactional customer communications, not staff reporting.',
        ],
        [
            'name' => 'DeliveryAgreementMail and DepositRefundRentalEndingMail references',
            'type' => 'UNKNOWN - INVESTIGATE BEFORE TOUCHING',
            'reason' => 'Controllers reference these classes, but matching class files were not found in app/Mail during audit.',
        ],
    ],

    'definitions' => [
        App\Support\Communications\DiscoveredTransactionalCommunicationCatalog::class,
    ],
];
