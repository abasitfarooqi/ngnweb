<?php

use Laravel\Fortify\Features;

return [

    'guard' => env('FORTIFY_GUARD', 'customer'),

    'passwords' => env('FORTIFY_PASSWORDS', 'customers'),

    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,

    'home' => env('FORTIFY_HOME', '/account'),

    'prefix' => '',
    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]),
    ],

];
