<?php

namespace App\Providers;

use App\Models\CustomerAuth;
use App\Support\AgreementPdfViewAssets;
use App\Support\AgreementDateTime;
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
use Illuminate\Support\Facades\Blade;
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
        $this->loadMigrationsFrom(database_path('migrations/LatestMigrationFiles/bootstrap'));
        $this->loadMigrationsFrom(database_path('migrations/LatestMigrationFiles'));

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

        View::composer('*', function (\Illuminate\View\View $view): void {
            $name = (string) $view->name();
            $needsPdfAssets = str_starts_with($name, 'livewire.agreements.')
                || str_starts_with($name, 'invoices.')
                || str_starts_with($name, 'portal.pdf.')
                || str_starts_with($name, 'pcn.template.')
                || str_starts_with($name, 'emails.pdf.');

            if (! $needsPdfAssets) {
                return;
            }

            $view->with(AgreementPdfViewAssets::composerVariables());

            if (str_starts_with($name, 'livewire.agreements.')) {
                AgreementDateTime::prepareViewData($view);
            }
        });

        // Ensure default view paths are included
        View::addLocation(resource_path('views'));

        // Legacy admin blades moved under livewire/agreements/migrated (RentingController still uses admin.* names).
        View::addLocation(resource_path('views/livewire/agreements/migrated'));

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

        // Flux Admin anonymous Blade components: <x-flux-admin::stat-card> etc.
        Blade::anonymousComponentPath(resource_path('views/flux-admin/components'), 'flux-admin');
    }
}
