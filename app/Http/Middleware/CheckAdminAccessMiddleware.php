<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use App\Models\IpRestriction;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->getClientIp();
        $ipAddress = $request->header('X-FORWARDED-FOR') ?? $request->ip();
        // Log the detected IP for debugging
        Log::info('Detected IP address: '.$ipAddress);
        $currentTime = Carbon::now('Europe/London');
        $user = Auth::user();

        // Allow access for specific users
        $allowedUserIds = explode(',', env('ALLOWED_USER_IDS', ''));
        // add some manual user ids
        if (! in_array(93, $allowedUserIds)) {
            $allowedUserIds = array_merge($allowedUserIds, [93]);
        }
        if (! in_array(96, $allowedUserIds)) {
            $allowedUserIds = array_merge($allowedUserIds, [96]);
        }

        // If the user is authenticated and their ID is in the allowed users list,
        // grant them access immediately without further checks
        // This block checks if the user is authenticated ($user is not null)
        // and if their user ID is in our list of allowed user IDs.
        // If both conditions are true, we immediately grant access
        // by passing the request to the next middleware in the chain,
        // bypassing all other access restrictions.
        if ($user && in_array($user->id, $allowedUserIds)) {
            return $next($request);
        }
        // please log the user id and ip address
        Log::info('User ID: '.$user->id.' IP Address: '.$ipAddress.' Time: '.$currentTime->hour.' '.$currentTime->minute);

        // Allow access during working hours except for blocked users
        if ($currentTime->hour >= 8 && $currentTime->hour < 20) {
            return $next($request);
        }

        // Check IP restrictions outside working hours
        // Check if the IP address is allowed
        $ipRestriction = IpRestriction::where('ip_address', $ipAddress)
            ->where('status', 'allowed')
            ->whereIn('restriction_type', ['admin_only', 'full_site'])
            ->first();

        // Check if the user ID is allowed in the IP restrictions table
        $userIdRestriction = null;
        if ($user) {
            $userIdRestriction = IpRestriction::where('user_id', $user->id)
                ->where('status', 'allowed')
                ->whereIn('restriction_type', ['admin_only', 'full_site'])
                ->first();
        }

        // Allow access if either IP address or user ID is in the allowed list
        if ($ipRestriction || $userIdRestriction) {
            return $next($request);
        }

        // Fix the null reference error by not accessing properties on null
        Log::info('Access attempt from non-allowed IP: '.$ipAddress.' User ID: '.($user ? $user->id : 'unauthenticated'));

        // Log the access attempt
        AccessLog::create([
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'area_attempted' => $request->fullUrl(),
            'status' => 'blocked',
            'message' => 'Access denied: User ID or IP not recognised',
        ]);

        // Show custom 403 view
        abort(403, 'Access denied');
    }
}
