<?php

// config/app.php
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Europe/London',

    'locale' => 'en',

    'fallback_locale' => 'en',

    'faker_locale' => 'en_GB',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    'providers' => ServiceProvider::defaultProviders()->merge([
        Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\RepositoryServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        App\Providers\BlockDangerousCommandsServiceProvider::class,

    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Bugsnag' => Bugsnag\BugsnagLaravel\Facades\Bugsnag::class,
    ])->toArray(),

];
