<?php

namespace App\Support;

use Illuminate\Auth\Access\AuthorizationException;

final class SpacesVaultAccess
{
    public static function authorize(): void
    {
        $user = FluxAdminAccess::user();

        if ($user === null) {
            abort(403);
        }

        if (FluxAdminAccess::isSuperAdmin($user)) {
            return;
        }

        $allowed = config('spaces-vault.allowed_user_ids', []);

        if ($allowed !== [] && in_array((int) $user->id, $allowed, true)) {
            return;
        }

        throw new AuthorizationException('You do not have access to this vault.');
    }
}
