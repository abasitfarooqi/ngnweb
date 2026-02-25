<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class EnsureCustomerEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user('customer') ||
            ($request->user('customer') instanceof MustVerifyEmail &&
            ! $request->user('customer')->hasVerifiedEmail())) {
            return response()->json([
                'message' => 'Your email address is not verified.',
                'verified' => false,
            ], 409);
        }

        return $next($request);
    }
}
