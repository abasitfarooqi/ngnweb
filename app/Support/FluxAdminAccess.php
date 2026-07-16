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

    public static function canFullClubAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();
        if ($user === null) {
            return false;
        }

        if (self::isAdmin($user)) {
            return true;
        }

        return method_exists($user, 'can') && $user->can('see-menu-club');
    }
}
