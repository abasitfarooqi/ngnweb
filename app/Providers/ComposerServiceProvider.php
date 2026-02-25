<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        view()->composer('partials.leftbar', function ($view) {
            $apps = \DB::table('system_application_users')
                ->join('system_applications', 'system_application_users.system_application_id', '=', 'system_applications.id')
                ->select('system_application_users.*', 'system_applications.*')
                ->where('system_application_users.user_id', auth()->user()->id)
                ->get();
            $view->with('apps', $apps);
        });
    }
}
