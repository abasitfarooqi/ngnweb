<?php

namespace App\Providers;

use App\Models\CustomerAuth;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayEnquiryRecord;
use App\Models\JudopayMitPaymentSession;
use App\Observers\JudopayCitPaymentSessionObserver;
use App\Observers\JudopayEnquiryRecordObserver;
use App\Observers\JudopayMitPaymentSessionObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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
        // Customer portal accounts must never use the staff `verification.verify` URL from
        // Illuminate\Auth\Notifications\VerifyEmail (that flow redirects to ngn-admin).
        VerifyEmail::createUrlUsing(function ($notifiable) {
            if ($notifiable instanceof CustomerAuth) {
                return URL::temporarySignedRoute(
                    'customer.verification.verify',
                    Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
                    [
                        'id' => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );
            }

            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

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
