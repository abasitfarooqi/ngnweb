<?php

namespace App\Http\Middleware;

use App\Support\FluxAdminAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! FluxAdminAccess::canEnterFluxAdmin($user)) {
            return redirect('/')->with('error', 'You do not have access to this area.');
        }

        return $next($request);
    }
}
