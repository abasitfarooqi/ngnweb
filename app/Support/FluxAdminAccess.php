<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;

final class FluxAdminAccess
{
    public const COMMUNICATIONS_PERMISSION = 'manage-communications';

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

    /**
     * Communications panel is Super Admin only, unless Super Admin assigns
     * manage-communications directly on a staff user (not via the Admin role).
     */
    public static function canAccessCommunications(?Authenticatable $user = null): bool
    {
        $user ??= self::user();

        if ($user === null) {
            return false;
        }

        if (self::isSuperAdmin($user)) {
            return true;
        }

        return method_exists($user, 'hasDirectPermission')
            && $user->hasDirectPermission(self::COMMUNICATIONS_PERMISSION);
    }
}
