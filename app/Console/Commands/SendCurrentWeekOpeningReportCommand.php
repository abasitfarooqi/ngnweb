<?php

namespace App\Console\Commands;

use App\Helpers\JudopayWeeklyMitSummary;
use App\Models\NgnMitQueue;
use App\Models\User;
use App\Notifications\MitWeeklyOpeningReportNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCurrentWeekOpeningReportCommand extends Command
{
    protected $signature = 'mit:send-week-start';
    protected $description = 'Send MIT Week Start report for current week (Monday report)';

    public function handle()
    {
        $this->info('Sending Week Start report for current week...');

        try {
            // Get current week
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            // Get weekly summary
            $summary = JudopayWeeklyMitSummary::getWeeklySummary($weekStart->format('Y-m-d'));

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

            if ($users->isEmpty()) {
                $this->warn('No users found with can-receive-mit-weekly-reports permission');
                return 1;
            }

            // Send notification to each user
            foreach ($users as $user) {
                $user->notify(new MitWeeklyOpeningReportNotification(
                    $summary,
                    $expectedItemsData,
                    $weekStart->format('l, M d, Y'),
                    $weekEnd->format('l, M d, Y')
                ));
            }

            $this->info("✓ Week Start report sent successfully to {$users->count()} users!");
            $this->info("  Expected: £" . number_format($summary['expected'], 2) . " ({$expectedItems->count()} items)");

        } catch (\Exception $e) {
            $this->error('Failed to send report: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
