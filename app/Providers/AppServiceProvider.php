<?php

namespace App\Providers;

use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayEnquiryRecord;
use App\Models\JudopayMitPaymentSession;
use App\Observers\JudopayCitPaymentSessionObserver;
use App\Observers\JudopayEnquiryRecordObserver;
use App\Observers\JudopayMitPaymentSessionObserver;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// Laravel\Cashier\Cashier - ignoreMigrations() removed in v15

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure default view paths are included
        View::addLocation(resource_path('views'));

        // Add error view path explicitly
        View::addLocation(resource_path('views/errors'));

        // Register SMS notification channel
        Notification::extend('sms', function ($app) {
            return $app->make(\App\Notifications\Channels\SmsChannel::class);
        });

        // Register Judopay model observers for automatic user_id injection
        JudopayCitPaymentSession::observe(JudopayCitPaymentSessionObserver::class);
        JudopayMitPaymentSession::observe(JudopayMitPaymentSessionObserver::class);
        JudopayEnquiryRecord::observe(JudopayEnquiryRecordObserver::class);
    }
}
