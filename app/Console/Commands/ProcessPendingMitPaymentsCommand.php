<?php

namespace App\Console\Commands;

use App\Models\JudopayMitQueue;
use App\Models\JudopayMitPaymentSession;
use App\Helpers\JudopayMit;
use App\Helpers\JudopayPayload;
use App\Services\JudopayService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessPendingMitPaymentsCommand extends Command
{
    protected $signature = 'judopay:process-pending-mit-payments
                            {--dry-run : Show what would be processed without actually processing or updating database}
                            {--short : In dry-run mode, show only a simple list of queue items (no detailed information)}
                            {--limit= : Limit the number of items to process}';

    protected $description = 'Manually process pending MIT payments for the CURRENT WEEK ONLY (Monday 00:00:00 to Sunday 23:59:59). Never processes past or future weeks.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        // Determine the CURRENT/STANDING week (Monday 00:00:00 to Sunday 23:59:59)
        // Week starts Monday and ends Sunday - same logic as WeeklyRentingReportCommand
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->setTime(0, 0, 0);
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY)->setTime(23, 59, 59);
        $weekStartDate = $weekStart->toDateString();
        $weekEndDate = $weekEnd->toDateString();

        if ($dryRun) {
            $this->warn('=== DRY RUN MODE - NO DATABASE UPDATES OR API CALLS WILL BE MADE ===');
            $this->newLine();
            $this->info('Current Week Scope:');
            $this->line("  Week Start: {$weekStart->format('Y-m-d H:i:s')} (Monday 00:00:00)");
            $this->line("  Week End:   {$weekEnd->format('Y-m-d H:i:s')} (Sunday 23:59:59)");
            $this->line("  Current Time: " . now()->format('Y-m-d H:i:s'));
            $this->newLine();
            $this->warn('⚠️  Only payments scheduled for THIS WEEK will be shown.');
            $this->warn('⚠️  Past weeks are excluded (what\'s passed is passed).');
            $this->warn('⚠️  Future weeks are excluded.');
            $this->newLine();
        } else {
            $this->info('=== Processing Pending MIT Payments (Current Week Only) ===');
            $this->newLine();
            $this->info('Current Week Scope:');
            $this->line("  Week Start: {$weekStart->format('Y-m-d H:i:s')} (Monday 00:00:00)");
            $this->line("  Week End:   {$weekEnd->format('Y-m-d H:i:s')} (Sunday 23:59:59)");
            $this->line("  Current Time: " . now()->format('Y-m-d H:i:s'));
            $this->newLine();
        }

        // Get items that should have fired but haven't
        // CRITICAL: Only process payments scheduled for the CURRENT WEEK
        // Never process past weeks (what's passed is passed)
        // Never process future weeks
        // EXCLUDE retry payments - only process first-time payments
        // A retry payment is identified by: same ngn_mit_queue_id with another record that was already fired
        $query = JudopayMitQueue::with([
            'ngnMitQueue.subscribable.subscribable',
        ])
            ->where('fired', false)
            ->whereBetween('mit_fire_date', [$weekStart, $weekEnd]) // ONLY current week
            ->where('retry', '<', config('judopay.mit.max_retry_attempts', 2))
            // EXCLUDE retry payments: if another record exists for same ngn_mit_queue_id that was fired, skip it
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('judopay_mit_queues as jmq2')
                    ->whereColumn('jmq2.ngn_mit_queue_id', 'judopay_mit_queues.ngn_mit_queue_id')
                    ->where('jmq2.fired', true)
                    ->whereColumn('jmq2.id', '!=', 'judopay_mit_queues.id');
            })
            ->orderBy('mit_fire_date', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        $dueItems = $query->get();

        if ($dueItems->isEmpty()) {
            $this->info('No pending MIT payments found for the current week.');
            $this->line("  Week: {$weekStartDate} to {$weekEndDate}");
            return Command::SUCCESS;
        }

        $this->info("Found {$dueItems->count()} pending MIT payment(s) for the current week:");
        $this->line("  Week: {$weekStartDate} to {$weekEndDate}");
        $this->newLine();

        if ($dryRun) {
            $short = $this->option('short');
            return $this->handleDryRun($dueItems, $short);
        }

        if (!$this->confirm('Do you want to process these payments?', true)) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('Processing payments...');
        $this->newLine();

        $processedCount = 0;
        $failedCount = 0;
        $progressBar = $this->output->createProgressBar($dueItems->count());
        $progressBar->start();

        foreach ($dueItems as $item) {
            try {
                Log::channel('judopay')->info('Processing pending MIT payment (manual command)', [
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
                    Log::channel('judopay')->info('Pending MIT payment processed successfully (manual command)', [
                        'judopay_mit_queue_id' => $item->id,
                        'payment_reference' => $item->judopay_payment_reference,
                    ]);
                } else {
                    $failedCount++;
                    Log::channel('judopay')->error('Pending MIT payment processing failed (manual command)', [
                        'judopay_mit_queue_id' => $item->id,
                        'error' => $result['message'],
                    ]);

                    // Increment retry count for failed items
                    $item->increment('retry');
                    $item->update(['fired' => false]); // Reset fired flag for retry
                }

            } catch (\Exception $e) {
                $failedCount++;
                Log::channel('judopay')->error('Pending MIT payment processing exception (manual command)', [
                    'judopay_mit_queue_id' => $item->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Increment retry count and reset fired flag
                $item->increment('retry');
                $item->update(['fired' => false]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Processing Complete ===');
        $this->table(
            ['Status', 'Count'],
            [
                ['Processed', $processedCount],
                ['Failed', $failedCount],
                ['Total', $dueItems->count()],
            ]
        );

        Log::channel('judopay')->info('Pending MIT payments manual processing complete', [
            'processed' => $processedCount,
            'failed' => $failedCount,
            'total' => $dueItems->count(),
        ]);

        return Command::SUCCESS;
    }

    /**
     * Handle dry-run mode - show exactly what would happen without making any changes
     * Only shows payments for the current week (Monday to Sunday)
     *
     * @param \Illuminate\Support\Collection $dueItems
     * @param bool $short If true, show only simple list without detailed information
     */
    private function handleDryRun($dueItems, bool $short = false): int
    {
        // Calculate current week boundaries for display
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->setTime(0, 0, 0);
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY)->setTime(23, 59, 59);

        $this->info('DRY RUN MODE: Showing what would be processed without making any changes.');
        $this->line("Current Week: {$weekStart->format('Y-m-d H:i:s')} to {$weekEnd->format('Y-m-d H:i:s')}");
        $this->newLine();

        // Short mode: show only simple list
        if ($short) {
            return $this->handleShortDryRun($dueItems, $weekStart, $weekEnd);
        }

        $itemNumber = 0;
        foreach ($dueItems as $item) {
            $itemNumber++;
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Payment #{$itemNumber} of {$dueItems->count()}");
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();

            // Basic Information
            $this->line('<fg=cyan>BASIC INFORMATION:</>');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Judopay MIT Queue ID', $item->id],
                    ['NGN MIT Queue ID', $item->ngn_mit_queue_id],
                    ['Payment Reference', $item->judopay_payment_reference],
                    ['Scheduled Fire Time', $item->mit_fire_date->format('Y-m-d H:i:s')],
                    ['Current Time', now()->format('Y-m-d H:i:s')],
                    ['Overdue By', $item->mit_fire_date->diffForHumans()],
                    ['Retry Count', $item->retry],
                    ['Max Retry Attempts', config('judopay.mit.max_retry_attempts', 2)],
                    ['Currently Fired', $item->fired ? 'Yes' : 'No'],
                ]
            );
            $this->newLine();

            // Check if related records exist
            $ngnMitQueue = $item->ngnMitQueue;
            if (!$ngnMitQueue) {
                $this->error('⚠️  NGN MIT Queue not found - would fail with: "NGN MIT Queue or subscription not found"');
                $this->newLine();
                continue;
            }

            $subscription = $ngnMitQueue->subscribable;
            if (!$subscription) {
                $this->error('⚠️  Subscription not found - would fail with: "NGN MIT Queue or subscription not found"');
                $this->newLine();
                continue;
            }

            $mitSession = JudopayMitPaymentSession::where('judopay_payment_reference', $item->judopay_payment_reference)->first();
            if (!$mitSession) {
                $this->error('⚠️  MIT Payment Session not found - would fail with: "MIT payment session not found"');
                $this->newLine();
                continue;
            }

            // Subscription Information
            $this->line('<fg=cyan>SUBSCRIPTION INFORMATION:</>');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Subscription ID', $subscription->id],
                    ['Consumer Reference', $subscription->consumer_reference],
                    ['Card Token', substr($subscription->card_token ?? 'N/A', 0, 20) . '...'],
                    ['JudoPay Receipt ID', $subscription->judopay_receipt_id ?? 'N/A'],
                    ['Subscribable Type', $subscription->subscribable_type ?? 'N/A'],
                    ['Subscribable ID', $subscription->subscribable_id ?? 'N/A'],
                ]
            );
            $this->newLine();

            // MIT Session Information
            $this->line('<fg=cyan>MIT PAYMENT SESSION:</>');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Session ID', $mitSession->id],
                    ['Payment Reference', $mitSession->judopay_payment_reference],
                    ['Order Reference', $mitSession->order_reference ?? 'N/A'],
                    ['Amount', '£' . number_format($mitSession->amount, 2)],
                    ['Description', $mitSession->description ?? 'N/A'],
                    ['Current Status', $mitSession->status ?? 'N/A'],
                    ['Attempt No', $mitSession->attempt_no ?? 'N/A'],
                ]
            );
            $this->newLine();

            // Extract metadata (same as processMitQueueItem does)
            try {
                $vrm = \App\Helpers\JudopayPayload::extractVrmFromSubscription($subscription);
            } catch (\Exception $e) {
                $vrm = 'N/A (Error: ' . $e->getMessage() . ')';
            }

            $invoiceNumber = $ngnMitQueue->invoice_number ?? 'N/A';
            $contractId = $subscription->subscribable_id ?? null;
            $contractType = $subscription->subscribable_type ? class_basename($subscription->subscribable_type) : 'N/A';

            // Metadata Information
            $this->line('<fg=cyan>PAYMENT METADATA:</>');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Invoice Number', $invoiceNumber],
                    ['VRM', $vrm],
                    ['Contract ID', $contractId ?? 'N/A'],
                    ['Contract Type', $contractType],
                ]
            );
            $this->newLine();

            // API Payload (what would be sent)
            $this->line('<fg=cyan>API PAYLOAD (What would be sent to JudoPay):</>');
            try {
                $payload = \App\Helpers\JudopayPayload::prepareMitPayload(
                    $subscription,
                    $mitSession,
                    $invoiceNumber,
                    $vrm,
                    $contractId,
                    $contractType,
                    $item->ngn_mit_queue_id
                );

                // Mask sensitive data
                $safePayload = $payload;
                if (isset($safePayload['cardToken'])) {
                    $safePayload['cardToken'] = substr($safePayload['cardToken'], 0, 20) . '... [MASKED]';
                }

                $this->line(json_encode($safePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            } catch (\Exception $e) {
                $this->error('⚠️  Error preparing payload: ' . $e->getMessage());
            }
            $this->newLine();

            // Database Updates (what would happen)
            $this->line('<fg=cyan>DATABASE UPDATES (What would be changed):</>');
            $this->warn('⚠️  In DRY RUN mode, NONE of these updates would be made.');
            $this->newLine();

            $updates = [];

            // Step 1: Update judopay_mit_queues.fired = true
            $updates[] = [
                'Table' => 'judopay_mit_queues',
                'Record ID' => $item->id,
                'Field' => 'fired',
                'Current Value' => $item->fired ? 'true' : 'false',
                'New Value' => 'true',
                'When' => 'Before API call',
            ];

            // Step 2: If API successful, update mit_attempt
            if ($ngnMitQueue->mit_attempt === 'not attempt') {
                $updates[] = [
                    'Table' => 'ngn_mit_queues',
                    'Record ID' => $ngnMitQueue->id,
                    'Field' => 'mit_attempt',
                    'Current Value' => $ngnMitQueue->mit_attempt ?? 'NULL',
                    'New Value' => 'first',
                    'When' => 'If API call successful',
                ];
            }

            // Step 3: Update MIT session status
            $updates[] = [
                'Table' => 'judopay_mit_payment_sessions',
                'Record ID' => $mitSession->id,
                'Field' => 'status',
                'Current Value' => $mitSession->status ?? 'NULL',
                'New Value' => 'success',
                'When' => 'If API call successful',
            ];

            $updates[] = [
                'Table' => 'judopay_mit_payment_sessions',
                'Record ID' => $mitSession->id,
                'Field' => 'payment_completed_at',
                'Current Value' => $mitSession->payment_completed_at ?? 'NULL',
                'New Value' => now()->format('Y-m-d H:i:s'),
                'When' => 'If API call successful',
            ];

            $updates[] = [
                'Table' => 'judopay_mit_payment_sessions',
                'Record ID' => $mitSession->id,
                'Field' => 'attempt_no',
                'Current Value' => $mitSession->attempt_no ?? 'NULL',
                'New Value' => $item->retry + 1,
                'When' => 'If API call successful',
            ];

            // Step 4: Create outcome record
            $updates[] = [
                'Table' => 'judopay_payment_session_outcomes',
                'Record ID' => 'NEW',
                'Field' => 'CREATE NEW RECORD',
                'Current Value' => 'N/A',
                'New Value' => 'New outcome record with API response',
                'When' => 'If API call successful',
            ];

            // Step 5: If API result is Success, update cleared status
            $updates[] = [
                'Table' => 'ngn_mit_queues',
                'Record ID' => $ngnMitQueue->id,
                'Field' => 'cleared',
                'Current Value' => $ngnMitQueue->cleared ? 'true' : 'false',
                'New Value' => 'true (if API result = Success)',
                'When' => 'If API result is Success',
            ];

            $updates[] = [
                'Table' => 'judopay_mit_queues',
                'Record ID' => $item->id,
                'Field' => 'cleared',
                'Current Value' => $item->cleared ? 'true' : 'false',
                'New Value' => 'true (if API result = Success)',
                'When' => 'If API result is Success',
            ];

            // Step 6: If failed, increment retry and reset fired
            $updates[] = [
                'Table' => 'judopay_mit_queues',
                'Record ID' => $item->id,
                'Field' => 'retry',
                'Current Value' => $item->retry,
                'New Value' => $item->retry + 1,
                'When' => 'If processing fails',
            ];

            $updates[] = [
                'Table' => 'judopay_mit_queues',
                'Record ID' => $item->id,
                'Field' => 'fired',
                'Current Value' => 'true',
                'New Value' => 'false',
                'When' => 'If processing fails (reset for retry)',
            ];

            $this->table(
                ['Table', 'Record ID', 'Field', 'Current Value', 'New Value', 'When'],
                $updates
            );
            $this->newLine();

            // API Call Information
            $this->line('<fg=cyan>API CALL INFORMATION:</>');
            try {
                $apiUrl = JudopayService::getApiUrl(config('judopay.endpoints.transactions'));
            } catch (\Exception $e) {
                $apiUrl = 'Error: ' . $e->getMessage();
            }
            $this->table(
                ['Field', 'Value'],
                [
                    ['API Endpoint', $apiUrl],
                    ['HTTP Method', 'POST'],
                    ['Headers', 'JudoPay authentication headers (Api-Version, Authorization)'],
                    ['Payload Size', isset($payload) ? strlen(json_encode($payload)) . ' bytes' : 'N/A'],
                    ['Would Make Real API Call', 'NO (DRY RUN)'],
                ]
            );
            $this->newLine();

            // Risk Assessment
            $this->line('<fg=yellow>RISK ASSESSMENT:</>');
            $risks = [];

            if ($item->retry >= config('judopay.mit.max_retry_attempts', 2) - 1) {
                $risks[] = ['Risk', '⚠️  HIGH: This is the last retry attempt'];
            }

            if (!$subscription->card_token) {
                $risks[] = ['Risk', '⚠️  HIGH: No card token available'];
            }

            if (!$mitSession) {
                $risks[] = ['Risk', '⚠️  HIGH: MIT session not found'];
            }

            if (empty($risks)) {
                $risks[] = ['Risk', '✅ No major risks identified'];
            }

            $this->table(['Assessment'], $risks);
            $this->newLine();
        }

        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->warn('DRY RUN COMPLETE - No database updates or API calls were made.');
        $this->info('To actually process these payments, run the command without --dry-run flag.');
        $this->newLine();
        $this->line("Note: Only payments for the current week ({$weekStart->format('Y-m-d')} to {$weekEnd->format('Y-m-d')}) are shown.");
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Handle short dry-run mode - show only simple list of queue items
     *
     * @param \Illuminate\Support\Collection $dueItems
     * @param Carbon $weekStart
     * @param Carbon $weekEnd
     */
    private function handleShortDryRun($dueItems, Carbon $weekStart, Carbon $weekEnd): int
    {
        $this->warn('SHORT MODE: Showing only queue items to be processed.');
        $this->newLine();

        $rows = [];
        foreach ($dueItems as $item) {
            $rows[] = [
                $item->id,
                $item->judopay_payment_reference,
                $item->mit_fire_date->format('Y-m-d H:i:s'),
                $item->retry,
                $item->fired ? 'Yes' : 'No',
            ];
        }

        $this->table(
            ['Queue ID', 'Payment Reference', 'Scheduled Time', 'Retry Count', 'Fired'],
            $rows
        );

        $this->newLine();
        $this->info("Total: {$dueItems->count()} payment(s) for current week ({$weekStart->format('Y-m-d')} to {$weekEnd->format('Y-m-d')})");
        $this->warn('DRY RUN - No database updates or API calls will be made.');
        $this->newLine();
        $this->line('Use --dry-run without --short to see detailed information for each payment.');

        return Command::SUCCESS;
    }
}

