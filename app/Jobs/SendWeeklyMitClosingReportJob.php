<?php

namespace App\Jobs;

use App\Helpers\JudopayWeeklyMitSummary;
use App\Models\User;
use App\Notifications\MitWeeklyClosingReportNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWeeklyMitClosingReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('=== WEEKLY MIT CLOSING REPORT - START ===', [
            'scheduled_time' => now()->format('Y-m-d H:i:s'),
        ]);

        try {
            // Get completed week (current week since this runs on Sunday night at 23:45)
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            Log::info('Processing closing report for week', [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
            ]);

            // Get weekly summary using existing helper
            $summary = JudopayWeeklyMitSummary::getWeeklySummary($weekStart->format('Y-m-d'));

            // Get detailed decline report with failure reasons
            $detailedDeclines = JudopayWeeklyMitSummary::getDetailedDeclineReport($weekStart->format('Y-m-d'));

            // Get success items (condensed for closing report)
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
                    Log::warning('Failed to extract VRM for closing report success item', [
                        'ngn_mit_queue_id' => $item->id,
                        'error' => $e->getMessage()
                    ]);
                }

                $customer = $item->subscribable->judopayOnboarding->onboardable ?? null;

                return [
                    'vrm' => $vrm,
                    'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'N/A',
                    'amount' => (float) ($item->subscribable->amount ?? 0),
                    'cleared_at' => $item->cleared_at ? $item->cleared_at->format('D, M d, H:i') : 'N/A',
                ];
            })->toArray();

            Log::info('Closing report data prepared', [
                'expected_amount' => $summary['expected'],
                'received_amount' => $summary['received'],
                'decline_amount' => $summary['decline'],
                'decline_count' => count($detailedDeclines),
                'success_count' => count($successItems),
            ]);

            // Get users with permission to receive reports
            $users = User::permission('can-receive-mit-weekly-reports')->get();

            if ($users->isEmpty()) {
                Log::warning('No users found with can-receive-mit-weekly-reports permission');
                return;
            }

            Log::info('Sending closing reports to users', [
                'user_count' => $users->count(),
            ]);

            // Send notification to each user
            foreach ($users as $user) {
                try {
                    $user->notify(new MitWeeklyClosingReportNotification(
                        $summary,
                        $detailedDeclines,
                        $successItems,
                        $weekStart->format('l, M d, Y'),
                        $weekEnd->format('l, M d, Y')
                    ));

                    Log::info('Closing report sent to user', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send closing report to user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('=== WEEKLY MIT CLOSING REPORT - COMPLETE ===', [
                'users_notified' => $users->count(),
                'decline_count' => count($detailedDeclines),
                'success_count' => count($successItems),
            ]);

        } catch (\Exception $e) {
            Log::error('Weekly MIT closing report job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
