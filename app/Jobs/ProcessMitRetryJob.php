<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Helpers\JudopayMit;
use App\Models\NgnMitQueue;

class ProcessMitRetryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * 
     * This job processes MIT retries for payment declines (not API errors).
     * It creates new MIT sessions with new payment references for failed payments.
     */
    public function handle(): void
    {
        Log::channel('judopay')->info('=== MIT RETRY PROCESSING START ===');
        
        $retryConfig = config('judopay.mit.retry_system');
        
        if (!$retryConfig['enabled']) {
            Log::channel('judopay')->info('MIT retry system is disabled');
            return;
        }

        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        // Find NGN MIT Queue items that need retry
        $itemsToRetry = NgnMitQueue::with('subscribable')
            ->where('status', 'sent') // Only items that were sent to live chamber
            ->where('cleared', false) // Only failed payments
            ->where('mit_attempt', 'first') // ONLY first attempt failed - ready for second attempt
            ->get();

        Log::channel('judopay')->info('Found MIT items for retry processing', [
            'count' => $itemsToRetry->count(),
            'retry_time' => $retryConfig['retry_time'],
        ]);

        foreach ($itemsToRetry as $ngnMitQueue) {
            try {
                Log::channel('judopay')->info('Processing MIT retry item', [
                    'ngn_mit_queue_id' => $ngnMitQueue->id,
                    'invoice_number' => $ngnMitQueue->invoice_number,
                    'current_attempt' => $ngnMitQueue->mit_attempt,
                    'subscription_id' => $ngnMitQueue->subscribable_id,
                ]);

                // Determine next attempt level
                $nextAttempt = 'second'; // ONLY ONE retry: first -> second -> manual
                
                // Create new MIT session and queue item for retry
                $result = JudopayMit::createRetryMitSession($ngnMitQueue, $nextAttempt);

                if ($result['success']) {
                    $processedCount++;
                    Log::channel('judopay')->info('MIT retry session created successfully', [
                        'ngn_mit_queue_id' => $ngnMitQueue->id,
                        'new_attempt' => $nextAttempt,
                        'new_payment_reference' => $result['data']['payment_reference'] ?? 'N/A',
                    ]);
                } else {
                    $errorCount++;
                    Log::channel('judopay')->error('Failed to create MIT retry session', [
                        'ngn_mit_queue_id' => $ngnMitQueue->id,
                        'error' => $result['message'],
                    ]);
                }

            } catch (\Exception $e) {
                $errorCount++;
                Log::channel('judopay')->error('Exception processing MIT retry item', [
                    'ngn_mit_queue_id' => $ngnMitQueue->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::channel('judopay')->info('=== MIT RETRY PROCESSING COMPLETE ===', [
            'processed' => $processedCount,
            'skipped' => $skippedCount,
            'errors' => $errorCount,
            'total_items' => $itemsToRetry->count(),
        ]);
    }
}
