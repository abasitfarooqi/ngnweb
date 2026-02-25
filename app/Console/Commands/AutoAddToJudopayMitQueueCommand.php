<?php

namespace App\Console\Commands;

use App\Helpers\JudopayMit;
use App\Models\NgnMitQueue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoAddToJudopayMitQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mit:auto-add-to-queue {--simulate : Run in simulation mode without database writes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically adds current week NGN MIT Queue items to Judopay MIT Queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isSimulation = $this->option('simulate');

        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->info('  AUTO-ADD NGN MIT QUEUE TO JUDOPAY MIT QUEUE');
        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->newLine();

        if ($isSimulation) {
            $this->warn('🔍 SIMULATION MODE - No database changes will be made');
            $this->newLine();
        }

        $this->info('📅 Current Time: ' . Carbon::now()->format('l, Y-m-d H:i:s'));
        $this->newLine();

        // Get current week boundaries
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $this->info('📌 Processing Week:');
        $this->line('   Start: ' . $weekStart->format('Y-m-d l'));
        $this->line('   End: ' . $weekEnd->format('Y-m-d l'));
        $this->newLine();

        // Get system user ID for authorization
        $systemUserId = config('judopay.mit.automation_user_id', 1);
        $this->info('👤 System User ID: ' . $systemUserId);
        $this->newLine();

        // Query NGN MIT Queue items for current week
        $queueItems = NgnMitQueue::with(['subscribable.judopayOnboarding.onboardable', 'subscribable.subscribable'])
            ->whereHas('subscribable') // Ensure subscription exists
            ->whereBetween('invoice_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->where('status', 'generated') // Only process generated items
            ->where('cleared', false) // Not cleared
            ->orderBy('invoice_date', 'asc')
            ->get();

        $totalItems = $queueItems->count();

        $this->info('📊 Found ' . $totalItems . ' eligible items (status=generated, cleared=false)');
        $this->newLine();

        if ($totalItems === 0) {
            $this->warn('⚠️  No items to process. Exiting.');

            Log::channel('judopay')->info('Auto-add to queue: No items found', [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'simulation' => $isSimulation,
            ]);

            return Command::SUCCESS;
        }

        // Process each item
        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $errors = [];

        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->info('  PROCESSING ITEMS');
        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->newLine();

        foreach ($queueItems as $index => $item) {
            $itemNumber = $index + 1;
            $subscription = $item->subscribable;

            // Get customer name
            $customerName = 'Unknown';
            if ($subscription && $subscription->judopayOnboarding && $subscription->judopayOnboarding->onboardable) {
                $customer = $subscription->judopayOnboarding->onboardable;
                $customerName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            }

            // Get VRM
            $vrm = 'N/A';
            if ($subscription && $subscription->subscribable) {
                if ($subscription->subscribable_type === 'App\Models\FinanceApplication') {
                    $vrm = $subscription->subscribable->application_items->first()->motorbike->vrm ?? 'N/A';
                } elseif ($subscription->subscribable_type === 'App\Models\RentingBooking') {
                    $vrm = $subscription->subscribable->rentingBookingItems->first()->motorbike->vrm ?? 'N/A';
                }
            }

            $this->line(sprintf(
                '[%02d/%02d] NGN Queue #%d | Invoice: %s | %s | %s | Fire: %s',
                $itemNumber,
                $totalItems,
                $item->id,
                $item->invoice_number,
                $customerName,
                $vrm,
                Carbon::parse($item->mit_fire_date)->format('Y-m-d H:i')
            ));

            if ($isSimulation) {
                // Simulation mode - just log what would happen
                $this->info('   ✓ WOULD ADD to Judopay MIT Queue');
                $successCount++;

                Log::channel('judopay')->info('Auto-add simulation', [
                    'ngn_mit_queue_id' => $item->id,
                    'invoice_number' => $item->invoice_number,
                    'customer' => $customerName,
                    'vrm' => $vrm,
                    'mit_fire_date' => $item->mit_fire_date,
                ]);

            } else {
                // Live mode - actually add to queue
                $result = JudopayMit::addToLiveChamber($item->id, $systemUserId);

                if ($result['success']) {
                    $this->info('   ✓ ADDED to Judopay MIT Queue');
                    $successCount++;

                    Log::channel('judopay')->info('Auto-add success', [
                        'ngn_mit_queue_id' => $item->id,
                        'invoice_number' => $item->invoice_number,
                        'customer' => $customerName,
                        'vrm' => $vrm,
                        'mit_fire_date' => $item->mit_fire_date,
                        'judopay_mit_queue_id' => $result['data']['judopay_mit_queue_id'] ?? null,
                    ]);

                } else {
                    $this->error('   ✗ FAILED: ' . $result['message']);
                    $failedCount++;
                    $errors[] = "Queue #{$item->id}: " . $result['message'];

                    Log::channel('judopay')->error('Auto-add failed', [
                        'ngn_mit_queue_id' => $item->id,
                        'invoice_number' => $item->invoice_number,
                        'error' => $result['message'],
                    ]);
                }
            }

            $this->newLine();
        }

        // Summary
        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->info('  SUMMARY');
        $this->info('═══════════════════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->info('Total Items: ' . $totalItems);
        $this->info('✓ Success: ' . $successCount);

        if ($failedCount > 0) {
            $this->error('✗ Failed: ' . $failedCount);
            $this->newLine();
            $this->error('Errors:');
            foreach ($errors as $error) {
                $this->line('  - ' . $error);
            }
        }

        $this->newLine();

        if ($isSimulation) {
            $this->warn('🔍 SIMULATION COMPLETE - No database changes made');
        } else {
            $this->info('✅ OPERATION COMPLETE');
        }

        $this->newLine();

        // Log summary
        Log::channel('judopay')->info('Auto-add to queue completed', [
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'total_items' => $totalItems,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'simulation' => $isSimulation,
            'system_user_id' => $systemUserId,
        ]);

        return $failedCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
