<?php

namespace App\Providers;

use App\Models\RentingPricing;
use App\Policies\RentingPricingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RentingPricing::class => RentingPricingPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
