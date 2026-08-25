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

    public static function canInvestigate(?Authenticatable $user = null): bool
    {
        $user ??= FluxAdminAccess::user();

        if ($user === null) {
            return false;
        }

        if (FluxAdminAccess::isSuperAdmin($user)) {
            return true;
        }

        $email = strtolower(trim((string) ($user->email ?? '')));
        $directorEmail = strtolower(trim(RentingReferralSettings::approvalReportTo()));
        if ($email !== '' && $email === $directorEmail) {
            return true;
        }

        $ids = config('renting_referrals.director_user_ids', [66]);
        $ids = is_array($ids) ? array_map('intval', $ids) : [66];

        return in_array((int) $user->getAuthIdentifier(), $ids, true);
    }
}
