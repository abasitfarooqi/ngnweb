<?php

// File: config/ecommerce.php
return [
    'live' => env('ECOMMERCE_LIVE', false),
    'portal' => env('ECOMMERCE_PORTAL', false),
    'url' => env('ECOMMERCE_URL', env('APP_URL').'/shop'),
    'online_payment' => env('ECOMMERCE_ONLINE_PAYMENT', true),
    'link_payment' => env('ECOMMERCE_LINK_PAYMENT', true),
    'store_pickup' => env('ECOMMERCE_STORE_PICKUP', true),
    'allow_register' => env('ECOMMERCE_ALLOW_REGISTER', true),
    'allow_login' => env('ECOMMERCE_ALLOW_LOGIN', true),
];
