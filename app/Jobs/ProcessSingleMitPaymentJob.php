<?php

namespace App\Jobs;

use App\Models\JudopayMitQueue;
use App\Helpers\JudopayMit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSingleMitPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $judopayMitQueueId
    ) {}

    public function handle(): void
    {
        Log::channel('judopay')->info('=== SINGLE MIT PAYMENT PROCESSING START ===', [
            'judopay_mit_queue_id' => $this->judopayMitQueueId,
            'scheduled_time' => now()->format('Y-m-d H:i:s'),
        ]);

        // Find the MIT queue item with necessary relationships
        // Note: subscribable's child relationships are loaded conditionally in extractVrmFromSubscription
        $mitQueueItem = JudopayMitQueue::with([
            'ngnMitQueue.subscribable.subscribable', // Load subscribable (RentingBooking or FinanceApplication)
        ])->find($this->judopayMitQueueId);

        if (!$mitQueueItem) {
            Log::channel('judopay')->error('MIT queue item not found for scheduled job', [
                'judopay_mit_queue_id' => $this->judopayMitQueueId,
            ]);
            return;
        }

        // Check if already fired (safety check)
        if ($mitQueueItem->fired) {
            Log::channel('judopay')->warning('MIT queue item already fired - skipping', [
                'judopay_mit_queue_id' => $this->judopayMitQueueId,
            ]);
            return;
        }

        // Check retry limit
        if ($mitQueueItem->retry >= config('judopay.mit.max_retry_attempts', 2)) {
            Log::channel('judopay')->warning('MIT queue item exceeded retry limit - skipping', [
                'judopay_mit_queue_id' => $this->judopayMitQueueId,
                'retry_count' => $mitQueueItem->retry,
            ]);
            return;
        }

        try {
            Log::channel('judopay')->info('Processing scheduled MIT payment', [
                'judopay_mit_queue_id' => $mitQueueItem->id,
                'ngn_mit_queue_id' => $mitQueueItem->ngn_mit_queue_id,
                'payment_reference' => $mitQueueItem->judopay_payment_reference,
                'scheduled_fire_time' => $mitQueueItem->mit_fire_date->format('Y-m-d H:i:s'),
                'actual_fire_time' => now()->format('Y-m-d H:i:s'),
                'retry_count' => $mitQueueItem->retry,
            ]);

            // Set fired = true BEFORE API call (optimistic approach)
            $mitQueueItem->update(['fired' => true]);

            // Process the MIT payment using existing helper
            $result = JudopayMit::processMitQueueItem($mitQueueItem);

            if ($result['success']) {
                Log::channel('judopay')->info('Scheduled MIT payment processed successfully', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'payment_reference' => $mitQueueItem->judopay_payment_reference,
                ]);
            } else {
                Log::channel('judopay')->error('Scheduled MIT payment processing failed', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'error' => $result['message'],
                ]);

                // Increment retry count for failed items
                $mitQueueItem->increment('retry');
                $mitQueueItem->update(['fired' => false]); // Reset fired flag for retry

                // Schedule retry if within limits
                if ($mitQueueItem->retry < config('judopay.mit.max_retry_attempts', 2)) {
                    $retryDelay = config('judopay.mit.retry_delay_hours', 12);
                    $retryTime = now()->addHours($retryDelay);
                    
                    Log::channel('judopay')->info('Scheduling MIT payment retry', [
                        'judopay_mit_queue_id' => $mitQueueItem->id,
                        'retry_attempt' => $mitQueueItem->retry,
                        'retry_time' => $retryTime->format('Y-m-d H:i:s'),
                    ]);

                    // Schedule retry
                    ProcessSingleMitPaymentJob::dispatch($mitQueueItem->id)
                        ->delay($retryTime);
                }
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Scheduled MIT payment processing exception', [
                'judopay_mit_queue_id' => $this->judopayMitQueueId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Increment retry count and reset fired flag
            $mitQueueItem->increment('retry');
            $mitQueueItem->update(['fired' => false]);

            // Schedule retry if within limits
            if ($mitQueueItem->retry < config('judopay.mit.max_retry_attempts', 2)) {
                $retryDelay = config('judopay.mit.retry_delay_hours', 12);
                $retryTime = now()->addHours($retryDelay);
                
                Log::channel('judopay')->info('Scheduling MIT payment retry after exception', [
                    'judopay_mit_queue_id' => $mitQueueItem->id,
                    'retry_attempt' => $mitQueueItem->retry,
                    'retry_time' => $retryTime->format('Y-m-d H:i:s'),
                ]);

                ProcessSingleMitPaymentJob::dispatch($mitQueueItem->id)
                    ->delay($retryTime);
            }
        }

        Log::channel('judopay')->info('=== SINGLE MIT PAYMENT PROCESSING COMPLETE ===', [
            'judopay_mit_queue_id' => $this->judopayMitQueueId,
        ]);
    }
}
