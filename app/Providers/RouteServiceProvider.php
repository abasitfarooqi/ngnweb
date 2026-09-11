<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/ngn-admin/dashboard';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1024)->by($request->user()?->id ?: $request->ip());
        });

        // Public forms can create records and, in some legacy paths, trigger
        // outbound mail. Keep their abuse budget separate from read-only API
        // traffic; a generic high API limit is not form protection.
        RateLimiter::for('public-form', function (Request $request) {
            return Limit::perMinute(10)
                ->by(hash('sha256', (string) $request->ip()))
                ->response(fn () => response()->json([
                    'message' => 'Too many submissions. Please try again later.',
                ], 429));
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            if (file_exists(base_path('routes/flux-admin.php'))) {
                Route::middleware(['web'])
                    ->prefix('flux-admin')
                    ->group(function () {
                        Route::get('/login', \App\Livewire\FluxAdmin\Pages\Auth\Login::class)
                            ->middleware('guest')
                            ->name('flux-admin.login');
                    });

                Route::middleware(['web', 'auth', 'admin', 'check.admin.access', 'flux.communications-only', 'flux.page-access'])
                    ->prefix('flux-admin')
                    ->group(base_path('routes/flux-admin.php'));
            }
        });
    }
}
