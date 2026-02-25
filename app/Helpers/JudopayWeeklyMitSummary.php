<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\NgnMitQueue;
use App\Models\JudopayMitQueue;

class JudopayWeeklyMitSummary
{
    /**
     * Get weekly summary of Expected, Received, and Decline amounts
     *
     * @param string|null $weekParam Week parameter in Y-m-d format (same as controller)
     * @return array ['expected' => float, 'received' => float, 'decline' => float]
     */
    public static function getWeeklySummary(?string $weekParam = null): array
    {
        // Same week calculation logic as controller
        if ($weekParam) {
            try {
                $weekStart = Carbon::parse($weekParam)->startOfWeek();
            } catch (\Exception $e) {
                // Invalid date, use current week
                $weekStart = Carbon::now()->startOfWeek();
            }
        } else {
            $weekStart = Carbon::now()->startOfWeek();
        }

        $weekEnd = $weekStart->copy()->endOfWeek();


        // Expected: All NgnMitQueue items where invoice_date is within week
        $expectedItems = NgnMitQueue::with('subscribable')
            ->whereHas('subscribable')
            ->whereBetween('invoice_date', [
                $weekStart->format('Y-m-d'),
                $weekEnd->format('Y-m-d')
            ])
            ->orderBy('invoice_date', 'asc')
            ->get();

        $expected = $expectedItems->sum(function ($item) {
            return (float) ($item->subscribable->amount ?? 0);
        });

        // Received: NgnMitQueue items where cleared=true AND cleared_at is within week
        // Using ngn_mit_queues.cleared as the source of truth for payment success
        $receivedItems = NgnMitQueue::with('subscribable')
            ->whereHas('subscribable')
            ->where('cleared', true)
            ->whereNotNull('cleared_at')
            ->whereBetween('cleared_at', [
                $weekStart->startOfDay(),
                $weekEnd->endOfDay()
            ])
            ->whereBetween('invoice_date', [
                $weekStart->format('Y-m-d'),
                $weekEnd->format('Y-m-d')
            ])
            ->orderBy('cleared_at', 'asc')
            ->get();

        $received = $receivedItems->sum(function ($item) {
            return (float) ($item->subscribable->amount ?? 0);
        });

        // Decline: Only items that were actually attempted (fired) but failed
        // Must have JudopayMitQueue record (attempted) with fired=true AND NgnMitQueue.cleared=false
        // AND the attempt happened in that week
        $declineItems = NgnMitQueue::with('subscribable')
            ->whereHas('subscribable')
            ->whereHas('judopayMitQueues', function($query) use ($weekStart, $weekEnd) {
                // Payment was attempted (fired) in this week
                $query->where('fired', true)
                    ->whereBetween('created_at', [
                        $weekStart->startOfDay(),
                        $weekEnd->endOfDay()
                    ]);
            })
            ->where('cleared', false) // Payment failed
            ->whereBetween('invoice_date', [
                $weekStart->format('Y-m-d'),
                $weekEnd->format('Y-m-d')
            ])
            ->orderBy('invoice_date', 'asc')
            ->get();

        $decline = $declineItems->sum(function ($item) {
            return (float) ($item->subscribable->amount ?? 0);
        });

        // Ensure decline is not negative
        $decline = max(0, $decline);

        return [
            'expected' => (float) $expected,
            'received' => (float) $received,
            'decline' => (float) $decline,
            'expectedItems' => $expectedItems,
            'receivedItems' => $receivedItems,
            'declineItems' => $declineItems,
        ];
    }

    /**
     * Get detailed decline report with VRM, customer details, and failure reasons
     *
     * @param string|null $weekParam Week parameter in Y-m-d format
     * @return array Array of decline items with full details
     */
    public static function getDetailedDeclineReport(?string $weekParam = null): array
    {
        // Same week calculation logic as getWeeklySummary
        if ($weekParam) {
            try {
                $weekStart = Carbon::parse($weekParam)->startOfWeek();
            } catch (\Exception $e) {
                $weekStart = Carbon::now()->startOfWeek();
            }
        } else {
            $weekStart = Carbon::now()->startOfWeek();
        }

        $weekEnd = $weekStart->copy()->endOfWeek();

        // Get declined items with all necessary relationships
        $declineItems = NgnMitQueue::with([
            'subscribable.judopayOnboarding.onboardable',
            'subscribable.subscribable',
            'judopayMitQueues' => function($query) {
                $query->where('fired', true)
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('subscribable')
        ->whereHas('judopayMitQueues', function($query) use ($weekStart, $weekEnd) {
            $query->where('fired', true)
                ->whereBetween('created_at', [
                    $weekStart->startOfDay(),
                    $weekEnd->endOfDay()
                ]);
        })
        ->where('cleared', false)
        ->whereBetween('invoice_date', [
            $weekStart->format('Y-m-d'),
            $weekEnd->format('Y-m-d')
        ])
        ->orderBy('invoice_date', 'asc')
        ->get();

        // Build detailed decline report
        $detailedDeclines = [];

        foreach ($declineItems as $item) {
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
                \Log::warning('Failed to extract VRM for decline report', [
                    'ngn_mit_queue_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Get customer details
            $customer = $item->subscribable->judopayOnboarding->onboardable ?? null;

            // Get failure reason from most recent MIT session
            $failureReason = 'Unknown';
            $attemptCount = $item->judopayMitQueues->count();

            if ($item->judopayMitQueues->isNotEmpty()) {
                $lastAttempt = $item->judopayMitQueues->first();

                // Try to get MIT payment session for failure reason
                $mitSession = \App\Models\JudopayMitPaymentSession::where('judopay_payment_reference', $lastAttempt->judopay_payment_reference)->first();

                if ($mitSession && $mitSession->failure_reason) {
                    $failureReason = $mitSession->failure_reason;
                } elseif ($mitSession && $mitSession->judopay_response) {
                    $failureReason = data_get($mitSession->judopay_response, 'message', 'Card declined');
                }
            }

            $detailedDeclines[] = [
                'ngn_mit_queue_id' => $item->id,
                'vrm' => $vrm,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'N/A',
                'customer_phone' => $customer->phone ?? 'N/A',
                'customer_email' => $customer->email ?? 'N/A',
                'amount' => (float) ($item->subscribable->amount ?? 0),
                'billing_frequency' => $item->subscribable->billing_frequency ?? 'N/A',
                'invoice_number' => $item->invoice_number,
                'invoice_date' => $item->invoice_date,
                'mit_attempt' => $item->mit_attempt,
                'attempt_count' => $attemptCount,
                'failure_reason' => $failureReason,
                'contract_type' => class_basename($item->subscribable->subscribable_type),
                'contract_id' => $item->subscribable->subscribable_id,
            ];
        }

        return $detailedDeclines;
    }
}

