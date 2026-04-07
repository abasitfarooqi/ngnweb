<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DvlaVehicleEnquiryService
{
    private const URL = 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';

    /**
     * @return array{ok: bool, body: ?array, status: int, message: ?string}
     */
    public function lookup(string $registrationNumber): array
    {
        $key = config('services.dvla.api_key');
        if ($key === null || $key === '') {
            return [
                'ok' => false,
                'body' => null,
                'status' => 0,
                'message' => 'DVLA service is not configured.',
            ];
        }

        try {
            $response = Http::timeout(20)->withHeaders([
                'x-api-key' => $key,
                'Content-Type' => 'application/json',
            ])->post(self::URL, [
                'registrationNumber' => $registrationNumber,
            ]);

            $status = $response->status();
            $json = $response->json();

            if (! $response->successful()) {
                $detail = $json['errors'][0]['detail'] ?? $json['message'] ?? null;
                $message = is_string($detail) ? $detail : 'We could not find details for that registration.';

                return ['ok' => false, 'body' => is_array($json) ? $json : null, 'status' => $status, 'message' => $message];
            }

            return ['ok' => true, 'body' => is_array($json) ? $json : [], 'status' => $status, 'message' => null];
        } catch (\Throwable $e) {
            Log::warning('DVLA vehicle enquiry failed', ['error' => $e->getMessage()]);

            return [
                'ok' => false,
                'body' => null,
                'status' => 0,
                'message' => 'We could not reach the DVLA. Please try again shortly.',
            ];
        }
    }
}
