<?php

namespace App\Http\Middleware;

use App\Support\FluxAdminAccess;
use App\Support\FluxAdminPageAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFluxAdminPageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $name = $route?->getName();

        if (! is_string($name) || $name === '' || $name === 'flux-admin.logout') {
            return $next($request);
        }

        $user = FluxAdminAccess::user() ?? $request->user();

        if (! FluxAdminPageAccess::allows($user, $name, $route?->parameter('module'))) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
