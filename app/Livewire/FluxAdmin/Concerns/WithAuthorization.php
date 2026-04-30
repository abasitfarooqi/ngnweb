<?php

namespace App\Livewire\FluxAdmin\Concerns;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * Gate-check a Spatie permission inside a Livewire mount(). Throws 403 if
 * the authenticated Backpack user does not have the permission (or is not
 * a super-admin).
 */
trait WithAuthorization
{
    protected function authorizeModule(string $permission): void
    {
        $user = backpack_user();

        if (! $user) {
            abort(403);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return;
        }

        $permissionExists = app(\Spatie\Permission\PermissionRegistrar::class)
            ->getPermissions()
            ->contains('name', $permission);

        if (! $permissionExists) {
            return;
        }

        if (method_exists($user, 'can') && $user->can($permission)) {
            return;
        }

        throw new AuthorizationException('You do not have permission to access this section.');
    }
}
