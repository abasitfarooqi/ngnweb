<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (! Auth::guard('customer')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
