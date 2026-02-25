<?php

namespace App\Jobs;

use App\Helpers\JudopayWeeklyMitSummary;
use App\Models\NgnMitQueue;
use App\Models\User;
use App\Notifications\MitWeeklyOpeningReportNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWeeklyMitOpeningReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('=== WEEKLY MIT OPENING REPORT - START ===', [
            'scheduled_time' => now()->format('Y-m-d H:i:s'),
        ]);

        try {
            // Get current week (Monday-Sunday)
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            Log::info('Processing opening report for week', [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
            ]);

            // Get weekly summary using existing helper
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
                    Log::warning('Failed to extract VRM for opening report', [
                        'ngn_mit_queue_id' => $item->id,
                        'error' => $e->getMessage()
                    ]);
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

            Log::info('Opening report data prepared', [
                'expected_count' => count($expectedItemsData),
                'expected_amount' => $summary['expected'],
            ]);

            // Get users with permission to receive reports
            $users = User::permission('can-receive-mit-weekly-reports')->get();

            if ($users->isEmpty()) {
                Log::warning('No users found with can-receive-mit-weekly-reports permission');
                return;
            }

            Log::info('Sending opening reports to users', [
                'user_count' => $users->count(),
            ]);

            // Send notification to each user
            foreach ($users as $user) {
                try {
                    $user->notify(new MitWeeklyOpeningReportNotification(
                        $summary,
                        $expectedItemsData,
                        $weekStart->format('l, M d, Y'),
                        $weekEnd->format('l, M d, Y')
                    ));

                    Log::info('Opening report sent to user', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send opening report to user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('=== WEEKLY MIT OPENING REPORT - COMPLETE ===', [
                'users_notified' => $users->count(),
                'expected_items' => count($expectedItemsData),
            ]);

        } catch (\Exception $e) {
            Log::error('Weekly MIT opening report job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
