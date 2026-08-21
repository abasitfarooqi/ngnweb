<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;

final class RentingReferralAccess
{
    public static function canView(?Authenticatable $user = null): bool
    {
        $user ??= FluxAdminAccess::user();

        if ($user === null) {
            return false;
        }

        return FluxAdminAccess::isSuperAdmin($user)
            || FluxAdminPageAccess::userHasPermission($user, 'see-menu-rentals');
    }

    public static function canReview(?Authenticatable $user = null): bool
    {
        $user ??= FluxAdminAccess::user();

        if ($user === null) {
            return false;
        }

        return FluxAdminAccess::isSuperAdmin($user)
            || FluxAdminPageAccess::userHasPermission($user, 'rental-referrals-review');
    }

    public static function isSuperAdmin(?Authenticatable $user = null): bool
    {
        return FluxAdminAccess::isSuperAdmin($user);
    }
}
