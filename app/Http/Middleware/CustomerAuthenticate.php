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

            // Store intended URL for redirect after login
            if ($request->is('accountinformation/*') && ! $request->is('accountinformation/login')) {
                session()->put('url.intended', $request->url());
            }

            return redirect()->route('customer.login');
        }

        return $next($request);
    }
}
