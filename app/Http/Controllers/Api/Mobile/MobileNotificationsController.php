<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CustomerAuth;
use App\Models\CustomerDeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileNotificationsController extends Controller
{
    public function registerDevice(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'in:expo,fcm,apns'],
            'platform' => ['nullable', 'string', 'in:ios,android'],
        ]);

        CustomerDeviceToken::query()->updateOrCreate(
            ['customer_auth_id' => $customer->id, 'token' => $payload['token']],
            [
                'provider' => $payload['provider'] ?? 'expo',
                'platform' => $payload['platform'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json(['message' => 'Device registered for push notifications.']);
    }

    public function unregisterDevice(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        CustomerDeviceToken::query()
            ->where('customer_auth_id', $customer->id)
            ->where('token', $payload['token'])
            ->update(['is_active' => false]);

        return response()->json(['message' => 'Device unregistered.']);
    }

    private function customer(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum');

        return $actor instanceof CustomerAuth ? $actor : null;
    }
}
