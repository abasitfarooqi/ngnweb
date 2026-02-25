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
use Carbon\Carbon;

class ProcessMitQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::channel('judopay')->info('=== MIT QUEUE PROCESSING START ===');
        
        // Get items that are due NOW (fire time has arrived)
        $dueItems = JudopayMitQueue::with(['ngnMitQueue.subscribable'])
            ->where('fired', false)
            ->where('mit_fire_date', '<=', now()) // Fire time has arrived
            ->where('retry', '<', config('judopay.mit.max_retry_attempts', 2))
            ->orderBy('mit_fire_date', 'asc')
            ->get();

        Log::channel('judopay')->info('Found MIT items due for processing', [
            'count' => $dueItems->count(),
            'current_time' => now()->format('Y-m-d H:i:s'),
        ]);

        $processedCount = 0;
        $failedCount = 0;

        foreach ($dueItems as $item) {
            try {
                Log::channel('judopay')->info('Processing MIT item', [
                    'judopay_mit_queue_id' => $item->id,
                    'ngn_mit_queue_id' => $item->ngn_mit_queue_id,
                    'payment_reference' => $item->judopay_payment_reference,
                    'scheduled_time' => $item->mit_fire_date->format('Y-m-d H:i:s'),
                    'retry_count' => $item->retry,
                ]);

                // Set fired = true BEFORE API call (optimistic approach)
                $item->update(['fired' => true]);

                // Process the MIT payment using existing helper
                $result = JudopayMit::processMitQueueItem($item);

                if ($result['success']) {
                    $processedCount++;
                    Log::channel('judopay')->info('MIT item processed successfully', [
                        'judopay_mit_queue_id' => $item->id,
                        'payment_reference' => $item->judopay_payment_reference,
                    ]);
                } else {
                    $failedCount++;
                    Log::channel('judopay')->error('MIT item processing failed', [
                        'judopay_mit_queue_id' => $item->id,
                        'error' => $result['message'],
                    ]);

                    // Increment retry count for failed items
                    $item->increment('retry');
                    $item->update(['fired' => false]); // Reset fired flag for retry
                }

            } catch (\Exception $e) {
                $failedCount++;
                Log::channel('judopay')->error('MIT item processing exception', [
                    'judopay_mit_queue_id' => $item->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Increment retry count and reset fired flag
                $item->increment('retry');
                $item->update(['fired' => false]);
            }
        }

        Log::channel('judopay')->info('=== MIT QUEUE PROCESSING COMPLETE ===', [
            'processed' => $processedCount,
            'failed' => $failedCount,
            'total_items' => $dueItems->count(),
        ]);
    }
}
