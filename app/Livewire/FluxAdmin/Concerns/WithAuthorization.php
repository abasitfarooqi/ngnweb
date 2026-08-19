<?php

namespace App\Livewire\FluxAdmin\Concerns;

use App\Support\FluxAdminAccess;
use App\Support\FluxAdminPageAccess;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Gate-check a Spatie permission inside a Livewire mount(). Throws 403 if
 * the authenticated Backpack user does not have the permission (or is not
 * a super-admin).
 */
trait WithAuthorization
{
    use CatchesUniqueConstraintViolations;

    protected function authorizeModule(string $permission): void
    {
        $user = backpack_user() ?? FluxAdminAccess::user();

        if (! $user) {
            abort(403);
        }

        $route = request()->route();
        $name = $route?->getName();

        if (is_string($name) && str_starts_with($name, 'flux-admin.')) {
            if (! FluxAdminPageAccess::allows($user, $name, $route?->parameter('module'))) {
                throw new AuthorizationException('You do not have permission to access this section.');
            }

            return;
        }

        if (FluxAdminAccess::isSuperAdmin($user)) {
            return;
        }

        if (FluxAdminPageAccess::userHasPermission($user, $permission)) {
            return;
        }

        throw new AuthorizationException('You do not have permission to access this section.');
    }

    protected function authorizeFullClubAdmin(): void
    {
        if (! FluxAdminAccess::isSuperAdmin()) {
            throw new AuthorizationException('You do not have permission to access this section.');
        }
    }

    protected function authorizeSuperAdmin(): void
    {
        if (! FluxAdminAccess::isSuperAdmin()) {
            throw new AuthorizationException('Only Super Admin can access this section.');
        }
    }
}
