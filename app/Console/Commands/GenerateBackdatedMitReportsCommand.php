<?php

namespace App\Console\Commands;

use App\Helpers\JudopayWeeklyMitSummary;
use App\Models\NgnMitQueue;
use App\Models\User;
use App\Notifications\MitWeeklyOpeningReportNotification;
use App\Notifications\MitWeeklyClosingReportNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBackdatedMitReportsCommand extends Command
{
    protected $signature = 'mit:backdated-reports {weeks=2}';
    protected $description = 'Generate backdated MIT reports for previous weeks';

    public function handle()
    {
        $weeksBack = (int) $this->argument('weeks');

        $this->info("Generating backdated reports for last {$weeksBack} weeks...");

        for ($i = $weeksBack; $i >= 1; $i--) {
            $this->generateReportsForWeek($i);
        }

        $this->info('All backdated reports generated successfully!');
    }

    private function generateReportsForWeek(int $weeksAgo)
    {
        // Calculate the target week
        $targetWeekStart = Carbon::now()->startOfWeek()->subWeeks($weeksAgo);
        $targetWeekEnd = $targetWeekStart->copy()->endOfWeek();

        $this->info("Generating reports for week: {$targetWeekStart->format('M d, Y')} - {$targetWeekEnd->format('M d, Y')}");

        // Get summary for that week
        $summary = JudopayWeeklyMitSummary::getWeeklySummary($targetWeekStart->format('Y-m-d'));

        // OPENING REPORT (Week Start)
        $this->generateOpeningReport($targetWeekStart, $targetWeekEnd, $summary);

        // CLOSING REPORT (Week End)
        $this->generateClosingReport($targetWeekStart, $targetWeekEnd, $summary);

        $this->line('');
    }

    private function generateOpeningReport($weekStart, $weekEnd, $summary)
    {
        $this->info('  → Generating Opening Report (Week Start)...');

        // Get expected items with full details including VRM
        $expectedItems = NgnMitQueue::with([
            'subscribable.judopayOnboarding.onboardable',
            'subscribable.subscribable',
        ])
        ->whereHas('subscribable')
        ->whereBetween('invoice_date', [
            $weekStart->format('Y-m-d'),
            $weekEnd->format('Y-m-d')
        ])
        ->orderBy('invoice_date', 'asc')
        ->get();

        // Load additional relationships for VRM extraction
        foreach ($expectedItems as $item) {
            if ($item->subscribable->subscribable_type === 'App\Models\FinanceApplication') {
                $item->subscribable->subscribable->load('application_items.motorbike');
            } elseif ($item->subscribable->subscribable_type === 'App\Models\RentingBooking') {
                $item->subscribable->subscribable->load('rentingBookingItems.motorbike');
            }
        }

        // Build expected items array with VRM
        $expectedItemsData = $expectedItems->map(function ($item) {
            // Extract VRM
            $vrm = 'N/A';
            try {
                if ($item->subscribable->subscribable_type === 'App\Models\FinanceApplication') {
                    $vrm = optional(optional($item->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? 'N/A';
                } elseif ($item->subscribable->subscribable_type === 'App\Models\RentingBooking') {
                    $vrm = optional(optional($item->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? 'N/A';
                }
            } catch (\Exception $e) {
                // Silent fail
            }

            $customer = $item->subscribable->judopayOnboarding->onboardable ?? null;

            return [
                'ngn_mit_queue_id' => $item->id,
                'vrm' => $vrm,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'N/A',
                'customer_phone' => $customer->phone ?? 'N/A',
                'amount' => (float) ($item->subscribable->amount ?? 0),
                'billing_frequency' => $item->subscribable->billing_frequency ?? 'N/A',
                'invoice_number' => $item->invoice_number,
                'invoice_date' => $item->invoice_date->format('D, M d, Y'),
                'mit_fire_date' => $item->mit_fire_date->format('D, M d, Y H:i'),
                'status' => $item->status,
                'contract_type' => class_basename($item->subscribable->subscribable_type),
            ];
        })->toArray();

        // Get users with permission
        $users = User::permission('can-receive-mit-weekly-reports')->get();

        // Send notification to each user
        foreach ($users as $user) {
            $user->notify(new MitWeeklyOpeningReportNotification(
                $summary,
                $expectedItemsData,
                $weekStart->format('l, M d, Y'),
                $weekEnd->format('l, M d, Y')
            ));
        }

        $this->info("     Sent to {$users->count()} users - {$expectedItems->count()} items, £" . number_format($summary['expected'], 2));
    }

    private function generateClosingReport($weekStart, $weekEnd, $summary)
    {
        $this->info('  → Generating Closing Report (Week End)...');

        // Get detailed decline report
        $detailedDeclines = JudopayWeeklyMitSummary::getDetailedDeclineReport($weekStart->format('Y-m-d'));

        // Get success items
        $successItems = $summary['receivedItems']->map(function ($item) {
            // Extract VRM
            $vrm = 'N/A';
            try {
                if ($item->subscribable->subscribable_type === 'App\Models\FinanceApplication') {
                    $item->subscribable->subscribable->load('application_items.motorbike');
                    $vrm = optional(optional($item->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? 'N/A';
                } elseif ($item->subscribable->subscribable_type === 'App\Models\RentingBooking') {
                    $item->subscribable->subscribable->load('rentingBookingItems.motorbike');
                    $vrm = optional(optional($item->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? 'N/A';
                }
            } catch (\Exception $e) {
                // Silent fail
            }

            $customer = $item->subscribable->judopayOnboarding->onboardable ?? null;

            return [
                'vrm' => $vrm,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'N/A',
                'amount' => (float) ($item->subscribable->amount ?? 0),
                'cleared_at' => $item->cleared_at ? $item->cleared_at->format('D, M d, H:i') : 'N/A',
            ];
        })->toArray();

        // Get users with permission
        $users = User::permission('can-receive-mit-weekly-reports')->get();

        // Send notification to each user
        foreach ($users as $user) {
            $user->notify(new MitWeeklyClosingReportNotification(
                $summary,
                $detailedDeclines,
                $successItems,
                $weekStart->format('l, M d, Y'),
                $weekEnd->format('l, M d, Y')
            ));
        }

        $this->info("     Sent to {$users->count()} users - Received: £" . number_format($summary['received'], 2) . ", Declined: £" . number_format($summary['decline'], 2) . " (" . count($detailedDeclines) . " items)");
    }
}
