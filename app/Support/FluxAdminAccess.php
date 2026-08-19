<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class FluxAdminAccess
{
    public const COMMUNICATIONS_PERMISSION = 'manage-communications';

    /** @var array<string, bool> */
    private static array $superAdminCache = [];

    public static function user(): ?Authenticatable
    {
        if (function_exists('backpack_user')) {
            $user = backpack_user();
            if ($user) {
                return $user;
            }
        }

        return auth()->user();
    }

    public static function isSuperAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();
        if ($user === null) {
            return false;
        }

        $id = (int) ($user->getAuthIdentifier() ?? 0);
        $key = $id > 0 ? 'id:'.$id : 'obj:'.spl_object_id($user);
        if (array_key_exists($key, self::$superAdminCache)) {
            return self::$superAdminCache[$key];
        }

        return self::$superAdminCache[$key] = self::resolveIsSuperAdmin($user);
    }

    public static function isAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && (self::isSuperAdmin($user) || self::userHasRole($user, 'Admin'));
    }

    public static function userHasNamedRole(?Authenticatable $user, string $roleName): bool
    {
        $user ??= self::user();

        return $user !== null && self::userHasRole($user, $roleName);
    }

    /** @return list<int> */
    public static function fullClubAdminUserIds(): array
    {
        $ids = config('flux-admin-menu.full_club_admin_user_ids', [65, 66, 93]);

        return array_values(array_map('intval', is_array($ids) ? $ids : []));
    }

    public static function canFullClubAdmin(?Authenticatable $user = null): bool
    {
        return self::isSuperAdmin($user);
    }

    /**
     * Policy panel: Super Admin, or anyone Super Admin granted
     * manage-communications on (user or role).
     */
    public static function canAccessCommunications(?Authenticatable $user = null): bool
    {
        return self::isSuperAdmin($user);
    }

    public static function canManageCommunications(?Authenticatable $user = null): bool
    {
        return self::canAccessCommunications($user);
    }

    public static function canViewCommunicationsLog(?Authenticatable $user = null): bool
    {
        return self::isSuperAdmin($user);
    }

    public static function canAssignCommunicationsPermission(?Authenticatable $user = null): bool
    {
        return self::isSuperAdmin($user);
    }

    public static function canEnterFluxAdmin(?Authenticatable $user = null): bool
    {
        $user ??= self::user();
        if ($user === null) {
            return false;
        }

        return (int) ($user->is_admin ?? 0) === 1
            || self::isSuperAdmin($user)
            || self::canAccessCommunications($user);
    }

    /** Granted communications access, but not a Flux admin account. */
    public static function isCommunicationsOnlyStaff(?Authenticatable $user = null): bool
    {
        $user ??= self::user();
        if ($user === null) {
            return false;
        }

        if ((int) ($user->is_admin ?? 0) === 1 || self::isSuperAdmin($user)) {
            return false;
        }

        return self::canAccessCommunications($user);
    }

    private static function resolveIsSuperAdmin(Authenticatable $user): bool
    {
        return self::userHasRole($user, 'Super Admin');
    }

    private static function userHasRole(Authenticatable $user, string $roleName): bool
    {
        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole($roleName) || $user->hasRole($roleName, 'web')) {
                    return true;
                }
            } catch (\Throwable) {
                // Fall through to table checks.
            }
        }

        $userId = (int) ($user->getAuthIdentifier() ?? 0);
        if ($userId <= 0) {
            return false;
        }

        $roleId = DB::table('roles')->where('name', $roleName)->value('id');
        if (! $roleId) {
            return false;
        }

        if (isset($user->role_id) && (int) $user->role_id === (int) $roleId) {
            return true;
        }

        return Schema::hasTable('role_users')
            && DB::table('role_users')
                ->where('role_id', $roleId)
                ->where('user_id', $userId)
                ->exists();
    }

    private static function userHasPermission(Authenticatable $user, string $permission): bool
    {
        try {
            if (method_exists($user, 'can') && $user->can($permission)) {
                return true;
            }

            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission, 'web')) {
                return true;
            }
        } catch (\Throwable) {
            return false;
        }

        return false;
    }
}
