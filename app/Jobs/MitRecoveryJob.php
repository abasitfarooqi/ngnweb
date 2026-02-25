<?php

namespace App\Jobs;

use App\Helpers\JudopayMit;
use App\Models\JudopayMitPaymentSession;
use App\Services\JudopayEnquiryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

// THIS IS RETRY FOR FAILED MIT OUTCOMES / YET TO BE IMPLEMENTED / DO NOT USE
class MitRecoveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // CENTRAL RETRY CONTROL - Check config first
        if (! config('judopay.mit.enable_automatic_retry', false)) {
            Log::channel('judopay')->info('MIT Recovery: Automatic retry disabled via config');

            return;
        }

        $failedSessions = $this->getRecoverableSessions();

        Log::channel('judopay')->info('MIT Recovery: Found sessions to analyze', [
            'count' => $failedSessions->count(),
        ]);

        foreach ($failedSessions as $session) {
            $this->attemptRecovery($session);
        }
    }

    /**
     * Get recent declined/error sessions that might be retryable.
     * Uses config values for retry limits and timing.
     */
    protected function getRecoverableSessions()
    {
        $maxRetries = config('judopay.mit.max_retry_attempts', 2);
        $delayHours = config('judopay.mit.retry_delay_hours', 12);

        return JudopayMitPaymentSession::query()
            ->whereIn('status', ['declined', 'error'])
            ->where('attempt_no', '<=', $maxRetries) // Use config value
            ->where('created_at', '>=', now()->subHours($delayHours)) // Use config delay
            ->with(['subscription', 'paymentSessionOutcomes' => function ($q) {
                $q->latest()->limit(1); // Last outcome for verification
            }])
            ->get();
    }

    protected function attemptRecovery(JudopayMitPaymentSession $session): void
    {
        try {
            Log::channel('judopay')->info('MIT Recovery: Analyzing session', [
                'session_id' => $session->id,
                'subscription_id' => $session->subscription_id,
                'status' => $session->status,
                'attempt_no' => $session->attempt_no,
            ]);

            // Get the latest outcome for this session
            $lastOutcome = $session->paymentSessionOutcomes->first();

            if (! $lastOutcome) {
                Log::warning('MIT Recovery: No outcome found for session', [
                    'session_id' => $session->id,
                ]);

                return;
            }

            // Make enquiry using the new service
            $enquiryRecord = JudopayEnquiryService::enquireOutcome($lastOutcome, 'recovery_check');

            if (! $enquiryRecord) {
                Log::error('MIT Recovery: Failed to create enquiry record', [
                    'session_id' => $session->id,
                    'outcome_id' => $lastOutcome->id,
                ]);

                return;
            }

            // Analyze enquiry results
            if (! $enquiryRecord->isSuccessful()) {
                $this->flagForManualReview($session, 'Enquiry API call failed');

                return;
            }

            // Check if payment was actually successful
            if ($enquiryRecord->isRemotelySuccessful()) {
                Log::channel('judopay')->info('MIT Recovery: Payment was actually successful', [
                    'session_id' => $session->id,
                    'amount_collected' => $enquiryRecord->amount_collected_remote,
                ]);

                // Update session to reflect actual success
                $session->update([
                    'status' => 'success',
                    'status_score' => 2, // Fully confirmed
                    'failure_reason' => 'Recovered - payment was actually successful',
                ]);

                return;
            }

            // Check if retryable based on enquiry analysis
            if ($enquiryRecord->is_retryable !== true) {
                $reason = $enquiryRecord->is_retryable === false
                    ? "Non-retryable: {$enquiryRecord->bank_response_category}"
                    : 'Unknown retryability';

                $this->flagForManualReview($session, $reason);

                return;
            }

            // Confirmed retryable - create new MIT attempt
            $this->executeRetry($session, $enquiryRecord);

        } catch (Throwable $e) {
            Log::error('MIT Recovery: Exception during recovery', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->flagForManualReview($session, 'Recovery exception: '.$e->getMessage());
        }
    }

    /**
     * Execute the actual retry
     */
    private function executeRetry(JudopayMitPaymentSession $session, $enquiryRecord): void
    {
        $newRef = 'MIT-RETRY-'.$session->subscription->consumer_reference.'-'.now()->timestamp;

        Log::channel('judopay')->info('MIT Recovery: Executing retry', [
            'session_id' => $session->id,
            'new_reference' => $newRef,
            'bank_code' => $enquiryRecord->external_bank_response_code,
            'category' => $enquiryRecord->bank_response_category,
        ]);

        $result = JudopayMit::processMitPayment(
            $session->subscription,
            $session->amount,
            $newRef,
            'Recovery attempt for declined payment'
        );

        if ($result['success']) {
            // Update original session as recovered
            $session->update([
                'failure_reason' => 'Recovered in attempt '.($session->attempt_no + 1),
            ]);

            Log::channel('judopay')->info('MIT Recovery: Retry successful', [
                'session_id' => $session->id,
                'new_session_id' => $result['data']['mit_session_id'] ?? null,
            ]);
        } else {
            Log::warning('MIT Recovery: Retry failed again', [
                'session_id' => $session->id,
                'error' => $result['message'] ?? 'Unknown error',
            ]);
        }
    }

    /**
     * Flag session for manual review (e.g., update status, notify staff).
     */
    protected function flagForManualReview(JudopayMitPaymentSession $session, string $reason): void
    {
        $session->update([
            'status' => 'manual_review',
            'failure_reason' => $reason,
        ]);

        // TODO: Notify staff (e.g., email, Slack) - implement per business needs
        Log::alert('MIT Recovery: Flagged for manual review', [
            'session_id' => $session->id,
            'subscription_id' => $session->subscription_id,
            'reason' => $reason,
        ]);
    }
}
