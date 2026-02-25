<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JudopayOnboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboardable_id',
        'onboardable_type',
        'is_onboarded',
    ];

    protected $casts = [
        'is_onboarded' => 'boolean',
    ];

    /*
    * A JudopayOnboarding can have many subscriptions
    * called JudopaySubscription. In order get registered on
    * recurring payment scheduler it is required to have a
    * a active subscription.
    *
    * The lifecycle of JudopayOnboarding require to be true
    * is_onboarded; which is require to have at lease one
    * active subscrition which must have an card token.
    */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(JudopaySubscription::class);
    }

    // Client's model which is what client uses to onboard to Judopay.
    // Can be User, Customer, Client, etc...
    public function onboardable(): MorphTo
    {
        return $this->morphTo();
    }
}
