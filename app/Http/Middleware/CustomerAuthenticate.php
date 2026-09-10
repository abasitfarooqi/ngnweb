<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth || ! $customerAuth->is_active || ! $customerAuth->customer?->is_active || ! $customerAuth->customer?->is_register) {
            if ($customerAuth) {
                Auth::guard('customer')->logout();
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
