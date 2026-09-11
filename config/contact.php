<?php

return [
    'captcha_enabled' => (bool) env('CONTACT_CAPTCHA_ENABLED', false),
    'minimum_submit_seconds' => 2,
    'rate_limit' => [
        'ip_attempts' => 5,
        'email_attempts' => 5,
        'decay_seconds' => 600,
    ],
    'duplicate_seconds' => 180,
    'api_duplicate_seconds' => 180,
];
