<?php

namespace App\Services;

use App\Models\CustomerDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications via the Expo push HTTP API. Works for any
 * Expo-managed or Expo-prebuild React Native app without needing Firebase
 * or Apple push certificates configured on this backend.
 *
 * If the mobile app is bare React Native with its own FCM/APNs integration
 * instead of Expo, swap the HTTP call below for that provider's endpoint —
 * the register/unregister API and device token storage stay the same.
 */
class ExpoPushNotificationService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToCustomer(int $customerAuthId, string $title, string $body, array $data = []): void
    {
        $tokens = CustomerDeviceToken::query()
            ->where('customer_auth_id', $customerAuthId)
            ->where('is_active', true)
            ->pluck('token')
            ->filter(fn (string $token) => str_starts_with($token, 'ExponentPushToken[') || str_starts_with($token, 'ExpoPushToken['))
            ->values();

        if ($tokens->isEmpty()) {
            return;
        }

        $this->send($tokens->all(), $title, $body, $data);
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<string, mixed>  $data
     */
    public function send(array $tokens, string $title, string $body, array $data = []): void
    {
        if ($tokens === []) {
            return;
        }

        $messages = array_map(static fn (string $token) => [
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'sound' => 'default',
        ], $tokens);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post(self::EXPO_PUSH_URL, $messages);

            if (! $response->successful()) {
                Log::warning('Expo push send failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            Log::warning('Expo push send exception: '.$e->getMessage());
        }
    }
}
