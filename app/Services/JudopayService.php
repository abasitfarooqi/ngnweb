<?php

namespace App\Services;

class JudopayService
{
    private static function getAuthorizationHeader(): string
    {
        $token = config('judopay.token');
        $secret = config('judopay.secret');

        if (! $token || ! $secret) {
            throw new \RuntimeException('JudoPay configuration not found');
        }

        return 'Basic '.base64_encode($token.':'.$secret);
    }

    public static function getHeaders(): array
    {
        return array_merge(
            config('judopay.headers', []),
            [
                'Api-Version' => config('judopay.api_version', '6.23'),
                'Authorization' => self::getAuthorizationHeader(),
            ]
        );
    }

    public static function getApiUrl(string $endpoint): string
    {
        // Defensive: Ensure endpoint is never null or empty (CRITICAL for queue context)
        // This is the final safety net - if empty string somehow gets here, use default
        if (empty($endpoint) || !is_string($endpoint)) {
            \Illuminate\Support\Facades\Log::channel('judopay')->error('getApiUrl received invalid/null endpoint, using default', [
                'original_endpoint_type' => gettype($endpoint),
                'original_endpoint_value' => $endpoint,
                'using_default' => '/transactions/refunds',
            ]);
            $endpoint = '/transactions/refunds';
        }

        $baseUrl = rtrim(config('judopay.base_url', ''), '/');

        if (empty($baseUrl)) {
            throw new \RuntimeException('JudoPay base URL not configured');
        }

        $endpoint = ltrim($endpoint, '/');

        return $baseUrl.'/'.$endpoint;
    }

    public static function generatePaymentReference(string $type, string $consumerReference): string
    {
        $format = config("judopay.reference_format.$type");
        $prefix = config("judopay.$type.reference_prefix");
        $timestamp = now()->timestamp;

        return str_replace([
            '{prefix}',
            '{consumer_reference}',
            '{timestamp}',
        ], [
            $prefix,
            $consumerReference,
            $timestamp,
        ], $format);
    }

    public static function validateWebhookAuth(string $username, string $password): bool
    {
        return hash_equals(config('judopay.webhook.username'), $username) &&
            hash_equals(config('judopay.webhook.password'), $password);
    }

    public static function getStaticConfig(): array
    {
        return [
            'judoId' => config('judopay.judo_id'),
            'currency' => config('judopay.currency', 'GBP'),
            'countryCode' => config('judopay.country_code', 826),
        ];
    }

    public static function getCitUrls(): array
    {
        return [
            'successUrl' => config('judopay.cit.success_url'),
            'cancelUrl' => config('judopay.cit.cancel_url'),
        ];
    }

    public static function getNetworkConfig(): array
    {
        return [
            'timeout' => config('judopay.timeout', 30),
            'retryAttempts' => config('judopay.retry_attempts', 3),
        ];
    }

    public static function isSandbox(): bool
    {
        return config('judopay.sandbox', true);
    }

    /**
     * Process a refund for a successful CIT payment
     *
     * @param array $payload Refund payload with keys: receiptId, amount, currency, yourPaymentReference
     * @return array ['success' => bool, 'data' => array|null, 'message' => string]
     */
    public static function processRefund(array $payload): array
    {
        try {
            // HARDCODED FALLBACK - Same approach as manual refunds
            // This ensures it works in queue context where config might not be loaded
            $defaultRefundEndpoint = '/transactions/refunds';

            // Try to get from config (works in HTTP context)
            $refundEndpoint = config('judopay.endpoints.refunds', $defaultRefundEndpoint);

            // If still null/empty, use hardcoded fallback (works in queue context)
            if (empty($refundEndpoint) || !is_string($refundEndpoint)) {
                \Illuminate\Support\Facades\Log::channel('judopay')->warning('Refund endpoint config unavailable, using hardcoded fallback', [
                    'config_value' => $refundEndpoint,
                    'using_fallback' => $defaultRefundEndpoint,
                    'context' => 'queue_worker',
                ]);
                $refundEndpoint = $defaultRefundEndpoint;
            }

            // Final type safety - ensure it's always a valid string
            $refundEndpoint = (string) $refundEndpoint;

            // Absolute last resort - if somehow still empty, use hardcoded value
            if (empty($refundEndpoint)) {
                $refundEndpoint = $defaultRefundEndpoint;
            }

            $url = self::getApiUrl($refundEndpoint);
            $headers = self::getHeaders();

            \Illuminate\Support\Facades\Log::channel('judopay')->info('Making JudoPay Refund API call', [
                'url' => $url,
                'endpoint' => $refundEndpoint,
                'headers' => \App\Helpers\JudopayLogSanitizer::sanitizeHeaders($headers),
                'payload' => \App\Helpers\JudopayLogSanitizer::sanitizeResponse($payload),
            ]);

            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->timeout(config('judopay.timeout', 30))
                ->post($url, $payload);

            $responseData = $response->json();

            \Illuminate\Support\Facades\Log::channel('judopay')->info('JudoPay Refund API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => \App\Helpers\JudopayLogSanitizer::sanitizeResponse($responseData),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'message' => 'Refund processed successfully',
                ];
            } else {
                return [
                    'success' => false,
                    'data' => $responseData,
                    'message' => 'Refund failed: ' . ($responseData['message'] ?? 'Unknown error'),
                ];
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::channel('judopay')->error('JudoPay Refund API exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => \App\Helpers\JudopayLogSanitizer::sanitizeResponse($payload),
            ]);

            return [
                'success' => false,
                'data' => null,
                'message' => 'Refund API call failed: ' . $e->getMessage(),
            ];
        }
    }
}
