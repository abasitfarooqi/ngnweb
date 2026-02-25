<?php

namespace App\Policies;

use App\Models\RentingPricing;
use App\Models\User;

class RentingPricingPolicy
{
    // use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RentingPricing $rentingPricing): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RentingPricing $rentingPricing): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RentingPricing $rentingPricing): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RentingPricing $rentingPricing): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RentingPricing $rentingPricing): bool
    {
        //
    }
}
