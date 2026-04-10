<?php

namespace App\Http\Controllers\Api\Mobile\V2\Concerns;

use Illuminate\Http\JsonResponse;

trait RewritesApiPayload
{
    private function rewriteApiPayload(JsonResponse $response): JsonResponse
    {
        $payload = $response->getData(true);

        $replacements = [
            '/api/v1/customer/support' => '/api/v2/mobile/customer/support',
            '/api/v1/staff/support' => '/api/v2/mobile/staff/support',
            '/api/v1/customer/login' => '/api/v2/mobile/auth/customer/login',
            '/api/v1/customer/register' => '/api/v2/mobile/auth/customer/register',
            '/api/v1/customer/forgot-password' => '/api/v2/mobile/auth/customer/forgot-password',
            '/api/v1/customer/reset-password' => '/api/v2/mobile/auth/customer/confirm-reset-password',
            '/api/v1/staff/login' => '/api/v2/mobile/auth/staff/login',
            '/api/v1/staff/me' => '/api/v2/mobile/auth/staff/me',
            '/api/v1/staff/logout' => '/api/v2/mobile/auth/staff/logout',
            '/api/v1/mobile' => '/api/v2/mobile',
        ];

        $payload = $this->replaceStringsRecursively($payload, $replacements);

        return response()->json($payload, $response->getStatusCode(), $response->headers->all());
    }

    private function replaceStringsRecursively(mixed $value, array $replacements): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->replaceStringsRecursively($item, $replacements);
            }

            return $value;
        }

        if (is_string($value)) {
            return str_replace(array_keys($replacements), array_values($replacements), $value);
        }

        return $value;
    }
}
