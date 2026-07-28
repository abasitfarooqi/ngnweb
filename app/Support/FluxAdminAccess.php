<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;

final class FluxAdminAccess
{
    public static function user(): ?Authenticatable
    {
        return function_exists('backpack_user') ? backpack_user() : auth()->user();
    }

    public static function isSuperAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && method_exists($user, 'hasRole')
            && $user->hasRole('Super Admin');
    }

    public static function isAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && method_exists($user, 'hasRole')
            && ($user->hasRole('Admin') || self::isSuperAdmin($user));
    }

    /** @return list<int> */
    public static function fullClubAdminUserIds(): array
    {
        $ids = config('flux-admin-menu.full_club_admin_user_ids', [65, 66, 93]);

        return array_values(array_map('intval', is_array($ids) ? $ids : []));
    }

    public static function canFullClubAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();
        if ($user === null) {
            return false;
        }

        if (self::isAdmin($user)) {
            return true;
        }

        $userId = (int) ($user->getAuthIdentifier() ?? 0);

        return $userId > 0 && in_array($userId, self::fullClubAdminUserIds(), true);
    }
}
