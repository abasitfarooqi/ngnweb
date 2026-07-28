<?php

namespace App\Support;

/**
 * Permission / visibility gates for Flux Admin global search.
 * Configure in config/flux-admin-search.php.
 */
final class FluxAdminSearchGate
{
    public static function allowsMenuRoute(string $routeName): bool
    {
        return self::allows(config('flux-admin-search.menu_routes.'.$routeName));
    }

    public static function allowsResource(string $modelClass): bool
    {
        return self::allows(config('flux-admin-search.resources.'.$modelClass));
    }

    /** @param  array{permission?: string, hidden?: bool, full_club_admin?: bool}|null  $rules */
    protected static function allows(?array $rules): bool
    {
        if ($rules === null) {
            return true;
        }

        if (! empty($rules['hidden'])) {
            return false;
        }

        $user = function_exists('backpack_user') ? backpack_user() : auth()->user();

        if (! empty($rules['full_club_admin'])) {
            return FluxAdminAccess::canFullClubAdmin($user);
        }

        $permission = $rules['permission'] ?? null;
        if ($permission === null || $permission === '') {
            return true;
        }

        if (! $user) {
            return false;
        }

        // Prefer explicit ability check (do not treat every Admin as search-omnipotent).
        if (method_exists($user, 'can') && $user->can($permission)) {
            return true;
        }

        return FluxAdminAccess::isSuperAdmin($user) || FluxAdminAccess::isAdmin($user);
    }
}
