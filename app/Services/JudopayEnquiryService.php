<?php

namespace App\Services;

use App\Models\JudopayEnquiryRecord;
use App\Models\JudopayPaymentSessionOutcome;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class JudopayEnquiryService
{
    /**
     * Make an enquiry for a specific outcome and store the response
     */
    public static function enquireOutcome(JudopayPaymentSessionOutcome $outcome, string $reason = 'manual_verification'): ?JudopayEnquiryRecord
    {
        if (! $outcome->isEnquiryEligible()) {
            Log::warning('Outcome not eligible for enquiry', [
                'outcome_id' => $outcome->id,
                'missing_identifier' => empty($outcome->enquiry_identifier),
                'missing_type' => empty($outcome->enquiry_type),
            ]);

            return null;
        }

        try {
            // Build the endpoint URL
            $endpoint = self::buildEndpointUrl($outcome->enquiry_type, $outcome->enquiry_identifier);

            Log::channel('judopay')->info('Making JudoPay enquiry', [
                'outcome_id' => $outcome->id,
                'enquiry_type' => $outcome->enquiry_type,
                'identifier' => $outcome->enquiry_identifier,
                'endpoint' => $endpoint,
                'reason' => $reason,
            ]);

            // Make the API call
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->timeout(config('judopay.timeout', 30))
                ->get(JudopayService::getApiUrl($endpoint));

            // Create the enquiry record
            $enquiryRecord = JudopayEnquiryRecord::create([
                'payment_session_outcome_id' => $outcome->id,
                'enquiry_type' => $outcome->enquiry_type,
                'enquiry_identifier' => $outcome->enquiry_identifier,
                'endpoint_used' => $endpoint,
                'api_status' => $response->successful() ? 'success' : 'failed',
                'http_status_code' => $response->status(),
                'api_response' => $response->json(),
                'api_headers' => $response->headers(),
                'enquired_at' => now(),
                'enquiry_reason' => $reason,
            ]);

            // Analyze the response and update the record
            self::analyzeEnquiryResponse($enquiryRecord, $outcome);

            Log::channel('judopay')->info('JudoPay enquiry completed', [
                'enquiry_record_id' => $enquiryRecord->id,
                'api_status' => $enquiryRecord->api_status,
                'http_status' => $enquiryRecord->http_status_code,
                'judopay_status' => $enquiryRecord->judopay_status,
                'matches_local' => $enquiryRecord->matches_local_record,
                'is_retryable' => $enquiryRecord->is_retryable,
            ]);

            return $enquiryRecord;

        } catch (Throwable $e) {
            Log::error('JudoPay enquiry failed', [
                'outcome_id' => $outcome->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Create a failed enquiry record
            return JudopayEnquiryRecord::create([
                'payment_session_outcome_id' => $outcome->id,
                'enquiry_type' => $outcome->enquiry_type,
                'enquiry_identifier' => $outcome->enquiry_identifier,
                'endpoint_used' => $endpoint ?? 'unknown',
                'api_status' => 'error',
                'http_status_code' => null,
                'api_response' => ['error' => $e->getMessage()],
                'api_headers' => [],
                'enquired_at' => now(),
                'enquiry_reason' => $reason,
            ]);
        }
    }

    /**
     * Build the correct endpoint URL based on enquiry type and identifier
     */
    private static function buildEndpointUrl(string $enquiryType, string $identifier): string
    {
        $endpointTemplate = config("judopay.endpoints.enquiry_{$enquiryType}");

        if ($enquiryType === 'transaction') {
            // For MIT: /transactions/{receiptId}
            return str_replace('{receiptId}', $identifier, $endpointTemplate);
        } else {
            // For CIT: /webpayments/{reference}
            return str_replace('{reference}', $identifier, $endpointTemplate);
        }
    }

    /**
     * Analyze the enquiry response and update the record with findings
     * Now handles REAL JudoPay API response formats and populates ALL new fields
     */
    private static function analyzeEnquiryResponse(JudopayEnquiryRecord $enquiryRecord, ?JudopayPaymentSessionOutcome $outcome): void
    {
        if (! $enquiryRecord->isSuccessful()) {
            $enquiryRecord->update([
                'judopay_status' => 'api_failed',
                'current_state' => 'unknown',
                'matches_local_record' => null,
                'discrepancy_notes' => 'API call failed, cannot verify status',
                'external_bank_response_code' => null,
                'amount_collected_remote' => null,
                'remote_message' => 'API call failed',
                'is_retryable' => null,
            ]);

            return;
        }

        $apiResponse = $enquiryRecord->api_response;

        // Extract status based on enquiry type (MIT vs CIT have different formats)
        if ($enquiryRecord->enquiry_type === 'transaction') {
            // MIT Transaction Response Format (based on your real API responses)
            $judopayStatus = data_get($apiResponse, 'result', 'unknown'); // "Success" or "Declined"
            $bankResponseCode = data_get($apiResponse, 'externalBankResponseCode', null); // "0", "5", etc.
            $message = data_get($apiResponse, 'message', ''); // "AuthCode: 412331" or "Card declined"
            $amountCollected = data_get($apiResponse, 'amountCollected', '0.00');
            $currentState = $judopayStatus; // For MIT, result IS the state

        } else {
            // CIT Webpayment Response Format (has nested receipt)
            $judopayStatus = data_get($apiResponse, 'status', 'unknown'); // Top-level status
            $receiptStatus = data_get($apiResponse, 'receipt.result', 'unknown'); // Receipt status
            $bankResponseCode = data_get($apiResponse, 'receipt.externalBankResponseCode', null);
            $message = data_get($apiResponse, 'receipt.message', '');
            $amountCollected = data_get($apiResponse, 'receipt.amountCollected', '0.00');
            $currentState = $receiptStatus ?: $judopayStatus; // Prefer receipt status
        }

        // Compare with local outcome (if available)
        $localStatus = $outcome ? $outcome->status : null;
        $matchesLocal = $outcome ? self::compareStatuses($localStatus, $judopayStatus, $currentState) : null;

        // Determine if retryable based on bank code and status
        $isRetryable = self::determineRetryability($judopayStatus, $bankResponseCode, $amountCollected);

        // Build comprehensive analysis
        $analysis = self::buildAnalysisNotes($outcome, $apiResponse, $enquiryRecord->enquiry_type);

        // Update ALL fields including the new ones
        $enquiryRecord->update([
            'judopay_status' => $judopayStatus,
            'current_state' => $currentState,
            'matches_local_record' => $matchesLocal,
            'discrepancy_notes' => $analysis['discrepancy_notes'],
            'external_bank_response_code' => $bankResponseCode,
            'amount_collected_remote' => $amountCollected ? floatval($amountCollected) : 0.00,
            'remote_message' => $message,
            'is_retryable' => $isRetryable,
        ]);
    }

    /**
     * Determine if a payment should be retried based on JudoPay response
     */
    private static function determineRetryability(string $judopayStatus, ?string $bankResponseCode, string $amountCollected): ?bool
    {
        // If payment was successful (amount collected > 0), don't retry
        if ($judopayStatus === 'Success' && floatval($amountCollected) > 0) {
            return false;
        }

        // If no bank code, can't determine
        if (! $bankResponseCode) {
            return null;
        }

        // Check bank response category
        $category = config("judopay.bank_response_codes.{$bankResponseCode}", 'UNKNOWN');

        // Define retryable categories based on your bank codes
        $retryableCategories = [
            'INSUFFICIENT_FUNDS', // 51 - might have funds later
            'DO_NOT_HONOR',       // 5 - general decline, might work later
            'RETRY',              // 9, 19, 28, 80 - explicitly retryable
            'TIMEOUT',            // 68 - network issue
            'NETWORK_ERROR',      // 91, 92 - network issue
            'SYSTEM_ERROR',       // 6, 30, 95, 96 - temporary system issue
        ];

        return in_array($category, $retryableCategories);
    }

    /**
     * Build detailed analysis notes comparing local vs remote data
     */
    private static function buildAnalysisNotes(?JudopayPaymentSessionOutcome $outcome, array $apiResponse, string $enquiryType): array
    {
        $notes = [];
        $localStatus = $outcome ? $outcome->status : 'standalone_enquiry';

        if ($enquiryType === 'transaction') {
            // MIT Analysis (based on your real responses)
            $remoteStatus = data_get($apiResponse, 'result', 'unknown');
            $remoteBankCode = data_get($apiResponse, 'externalBankResponseCode', 'unknown');
            $remoteAmount = data_get($apiResponse, 'amountCollected', '0.00');
            $remoteMessage = data_get($apiResponse, 'message', '');
            $receiptId = data_get($apiResponse, 'receiptId', 'unknown');

            $notes[] = "MIT Transaction {$receiptId}";
            $notes[] = "Local: {$localStatus} | Remote: {$remoteStatus}";
            $notes[] = "Bank Code: {$remoteBankCode} (".config("judopay.bank_response_codes.{$remoteBankCode}", 'UNKNOWN').')';
            $notes[] = "Amount Collected: £{$remoteAmount}";
            $notes[] = "Message: {$remoteMessage}";

            // Check specific discrepancies (only if we have local data to compare)
            if ($outcome) {
                if ($localStatus === 'success' && $remoteAmount === '0.00') {
                    $notes[] = '⚠️ Local shows success but no amount collected remotely';
                }

                if ($localStatus === 'declined' && $remoteStatus === 'Success' && floatval($remoteAmount) > 0) {
                    $notes[] = '🚨 MAJOR DISCREPANCY: Local declined but remote successful with amount collected';
                }
            } else {
                $notes[] = 'ℹ️ Standalone enquiry - no local data for comparison';
            }

        } else {
            // CIT Analysis (based on your real responses)
            $remoteStatus = data_get($apiResponse, 'status', 'unknown');
            $receiptStatus = data_get($apiResponse, 'receipt.result', 'unknown');
            $remoteBankCode = data_get($apiResponse, 'receipt.externalBankResponseCode', 'unknown');
            $remoteAmount = data_get($apiResponse, 'receipt.amountCollected', '0.00');
            $reference = data_get($apiResponse, 'reference', 'unknown');

            $notes[] = "CIT Webpayment {$reference}";
            $notes[] = "Local: {$localStatus} | Remote Status: {$remoteStatus} | Receipt: {$receiptStatus}";
            $notes[] = "Bank Code: {$remoteBankCode} (".config("judopay.bank_response_codes.{$remoteBankCode}", 'UNKNOWN').')';
            $notes[] = "Amount Collected: £{$remoteAmount}";

            if ($remoteStatus !== $receiptStatus) {
                $notes[] = "⚠️ Status mismatch between top-level ({$remoteStatus}) and receipt ({$receiptStatus})";
            }
        }

        return [
            'discrepancy_notes' => implode(' | ', $notes),
            'has_major_discrepancy' => str_contains(implode(' ', $notes), '🚨'),
        ];
    }

    /**
     * Compare local status with JudoPay status (updated for real API responses)
     */
    private static function compareStatuses(string $localStatus, string $judopayStatus, ?string $currentState = null): ?bool
    {
        // Normalize statuses for comparison
        $localNormalized = strtolower(trim($localStatus));
        $judopayNormalized = strtolower(trim($judopayStatus));
        $currentNormalized = $currentState ? strtolower(trim($currentState)) : null;

        // Direct match
        if ($localNormalized === $judopayNormalized) {
            return true;
        }

        // Check against current state too
        if ($currentNormalized && $localNormalized === $currentNormalized) {
            return true;
        }

        // Known equivalents based on real API responses
        $equivalents = [
            'success' => ['success', 'succeeded', 'completed'],
            'declined' => ['declined', 'failed', 'rejected'],
            'refunded' => ['refunded', 'refund', 'reversed'],
            'error' => ['error', 'failed', 'exception'],
        ];

        foreach ($equivalents as $localKey => $judopayVariants) {
            if ($localNormalized === $localKey && in_array($judopayNormalized, $judopayVariants)) {
                return true;
            }
            if ($currentNormalized && $localNormalized === $localKey && in_array($currentNormalized, $judopayVariants)) {
                return true;
            }
        }

        // If we can't determine, return false (mismatch detected)
        return false;
    }

    /**
     * Determine if an outcome should be retried based on enquiry results
     */
    public static function shouldRetryBasedOnEnquiry(JudopayEnquiryRecord $enquiryRecord): ?bool
    {
        // Check central config first
        if (! config('judopay.mit.enable_automatic_retry', false)) {
            return false; // Retry disabled globally
        }

        if (! $enquiryRecord->isSuccessful()) {
            return null; // Can't determine - API call failed
        }

        // Use the is_retryable field we calculated during analysis
        return $enquiryRecord->is_retryable;
    }

    /**
     * Enquire multiple outcomes in batch
     */
    public static function enquireMultipleOutcomes(array $outcomes, string $reason = 'batch_verification'): array
    {
        $results = [];

        foreach ($outcomes as $outcome) {
            if ($outcome instanceof JudopayPaymentSessionOutcome) {
                $results[] = self::enquireOutcome($outcome, $reason);

                // Add delay to respect API rate limits
                $delaySeconds = config('judopay.mit.dispatch_delay_seconds', 5);
                if ($delaySeconds > 0) {
                    sleep($delaySeconds);
                }
            }
        }

        return array_filter($results); // Remove null results
    }

    /**
     * Get outcomes that need enquiry (failed/declined without recent enquiry)
     */
    public static function getOutcomesNeedingEnquiry(int $hoursAgo = 24): array
    {
        return JudopayPaymentSessionOutcome::whereIn('status', ['declined', 'error'])
            ->where('occurred_at', '>=', now()->subHours($hoursAgo))
            ->whereDoesntHave('enquiryRecords', function ($query) use ($hoursAgo) {
                $query->where('enquired_at', '>=', now()->subHours($hoursAgo / 2));
            })
            ->get()
            ->toArray();
    }

    // ========================================
    // STANDALONE ENQUIRY METHODS
    // Independent of outcome records - for accountants, manual verification
    // ========================================

    /**
     * Direct CIT enquiry by reference (independent method)
     * Can be used by accountants or manual verification without outcome records
     */
    public static function enquireCitByReference(string $reference, string $reason = 'manual_enquiry'): ?JudopayEnquiryRecord
    {
        try {
            Log::channel('judopay')->info('Direct CIT enquiry by reference', [
                'reference' => $reference,
                'reason' => $reason,
            ]);

            // Build the endpoint URL
            $endpoint = self::buildEndpointUrl('webpayment', $reference);

            // Make the API call
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->timeout(config('judopay.timeout', 30))
                ->get(JudopayService::getApiUrl($endpoint));

            // Create the enquiry record without outcome dependency
            $enquiryRecord = JudopayEnquiryRecord::create([
                'payment_session_outcome_id' => null, // No outcome required
                'enquiry_type' => 'webpayment',
                'enquiry_identifier' => $reference,
                'endpoint_used' => $endpoint,
                'api_status' => $response->successful() ? 'success' : 'failed',
                'http_status_code' => $response->status(),
                'api_response' => $response->successful() ? $response->json() : null,
                'api_headers' => $response->headers(),
                'enquired_at' => now(),
                'enquiry_reason' => $reason,
            ]);

            // Analyze the response if successful
            if ($response->successful()) {
                self::analyzeEnquiryResponse($enquiryRecord, null);
            } else {
                $enquiryRecord->update([
                    'judopay_status' => 'api_failed',
                    'current_state' => 'api_failed',
                    'remote_message' => 'API call failed',
                    'matches_local_record' => null, // Cannot compare without outcome
                    'is_retryable' => false,
                ]);
            }

            return $enquiryRecord;

        } catch (Throwable $e) {
            Log::error('Direct CIT enquiry failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Direct MIT enquiry by receipt ID (independent method)
     * Can be used by accountants or manual verification without outcome records
     */
    public static function enquireMitByReceiptId(string $receiptId, string $reason = 'manual_enquiry'): ?JudopayEnquiryRecord
    {
        try {
            Log::channel('judopay')->info('Direct MIT enquiry by receipt ID', [
                'receipt_id' => $receiptId,
                'reason' => $reason,
            ]);

            // Build the endpoint URL
            $endpoint = self::buildEndpointUrl('transaction', $receiptId);

            // Make the API call
            $response = Http::withHeaders(JudopayService::getHeaders())
                ->timeout(config('judopay.timeout', 30))
                ->get(JudopayService::getApiUrl($endpoint));

            // Create the enquiry record without outcome dependency
            $enquiryRecord = JudopayEnquiryRecord::create([
                'payment_session_outcome_id' => null, // No outcome required
                'enquiry_type' => 'transaction',
                'enquiry_identifier' => $receiptId,
                'endpoint_used' => $endpoint,
                'api_status' => $response->successful() ? 'success' : 'failed',
                'http_status_code' => $response->status(),
                'api_response' => $response->successful() ? $response->json() : null,
                'api_headers' => $response->headers(),
                'enquired_at' => now(),
                'enquiry_reason' => $reason,
            ]);

            // Analyze the response if successful
            if ($response->successful()) {
                self::analyzeEnquiryResponse($enquiryRecord, null);
            } else {
                $enquiryRecord->update([
                    'judopay_status' => 'api_failed',
                    'current_state' => 'api_failed',
                    'remote_message' => 'API call failed',
                    'matches_local_record' => null, // Cannot compare without outcome
                    'is_retryable' => false,
                ]);
            }

            return $enquiryRecord;

        } catch (Throwable $e) {
            Log::error('Direct MIT enquiry failed', [
                'receipt_id' => $receiptId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Batch enquiry method - enquire multiple identifiers at once
     * Useful for accountants checking multiple transactions
     */
    public static function enquireBatch(array $enquiries, string $reason = 'batch_enquiry'): array
    {
        $results = [];

        foreach ($enquiries as $enquiry) {
            $type = $enquiry['type'] ?? null; // 'cit' or 'mit'
            $identifier = $enquiry['identifier'] ?? null;

            if (! $type || ! $identifier) {
                continue;
            }

            $record = null;
            if ($type === 'cit') {
                $record = self::enquireCitByReference($identifier, $reason);
            } elseif ($type === 'mit') {
                $record = self::enquireMitByReceiptId($identifier, $reason);
            }

            if ($record) {
                $results[] = $record;
            }

            // Rate limiting - respect API limits
            sleep(config('judopay.mit.dispatch_delay_seconds', 5));
        }

        return $results;
    }
}
