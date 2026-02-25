<?php

namespace App\Jobs;

use App\Helpers\NgnMitQueueMaker;
use App\Helpers\JudopayMit;
use App\Models\NgnMitQueue;
use App\Models\JudopaySubscription;
use App\Models\RentingBooking;
use App\Models\FinanceApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProduceNgnMitQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Delegate to JudopayMit helper for clean separation of concerns
        $result = JudopayMit::generateMitQueue();
        
        if ($result['success']) {
            Log::info("MIT Queue generation completed successfully", [
                'created_count' => $result['created_count'],
                'skipped_count' => $result['skipped_count']
            ]);
        } else {
            Log::error("MIT Queue generation failed", [
                'error' => $result['error'] ?? 'Unknown error'
            ]);
            throw new \Exception($result['message']);
        }
    }
}
