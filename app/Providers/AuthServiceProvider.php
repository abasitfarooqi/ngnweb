<?php

namespace App\Providers;

use App\Models\RentingPricing;
use App\Models\SupportConversation;
use App\Policies\RentingPricingPolicy;
use App\Policies\SupportConversationPolicy;
use App\Support\FluxAdminAccess;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RentingPricing::class => RentingPricingPolicy::class,
        SupportConversation::class => SupportConversationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user && FluxAdminAccess::isSuperAdmin($user)) {
                return true;
            }

            return null;
        });
    }
}
