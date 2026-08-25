<?php

namespace App\Support;

/**
 * Permission / visibility gates for Flux Admin global search.
 * Configure in config/flux-admin-search.php.
 */
final class FluxAdminSearchGate
{
    public static function allowsMenuRoute(string $routeName, bool $ignoreHidden = false): bool
    {
        $rules = config('flux-admin-search.menu_routes.'.$routeName);

        if (! $ignoreHidden && is_array($rules) && ! empty($rules['hidden'])) {
            return false;
        }

        if (is_array($rules) && (! empty($rules['super_admin']) || ! empty($rules['full_club_admin']))) {
            return FluxAdminAccess::isSuperAdmin();
        }

        return FluxAdminPageAccess::allows(FluxAdminAccess::user(), $routeName);
    }

    public static function allowsResource(string $modelClass): bool
    {
        return self::allows(config('flux-admin-search.resources.'.$modelClass));
    }

    /** @param  array{permission?: string, hidden?: bool, super_admin?: bool, full_club_admin?: bool}|null  $rules */
    protected static function allows(?array $rules): bool
    {
        if ($rules === null) {
            return true;
        }

        if (! empty($rules['hidden'])) {
            return false;
        }

        $user = FluxAdminAccess::user();

        if (! empty($rules['super_admin']) || ! empty($rules['full_club_admin'])) {
            return FluxAdminAccess::isSuperAdmin($user);
        }

        $permission = $rules['permission'] ?? null;
        if ($permission === null || $permission === '') {
            return true;
        }

        return FluxAdminPageAccess::allowsRequirement($user, ['permission' => $permission]);
    }
}
