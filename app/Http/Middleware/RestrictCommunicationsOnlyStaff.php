<?php

namespace App\Http\Middleware;

use App\Support\FluxAdminAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictCommunicationsOnlyStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! FluxAdminAccess::isCommunicationsOnlyStaff()) {
            return $next($request);
        }

        if ($request->routeIs('flux-admin.communications.*', 'flux-admin.logout', 'flux-admin.unread-badges')) {
            return $next($request);
        }

        return redirect()->to(FluxAdminAccess::homeRoute());
    }
}
