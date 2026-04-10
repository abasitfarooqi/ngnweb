<?php

namespace App\Providers;

use App\Models\RentingPricing;
use App\Models\SupportConversation;
use App\Policies\RentingPricingPolicy;
use App\Policies\SupportConversationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RentingPricing::class => RentingPricingPolicy::class,
        SupportConversation::class => SupportConversationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
