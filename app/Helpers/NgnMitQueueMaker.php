<?php

namespace App\Helpers;

use App\Http\Requests\JudopayCitPaymentSessionRequest;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayPaymentSessionOutcome;
use App\Models\JudopaySubscription;
use App\Models\FinanceApplication;
use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Services\JudopayService;
use Carbon\Carbon;
use App\Helpers\JudopayLogSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NgnMitQueueMaker
{
    // Only Make Coming week's invoices.
    public static function makeNgnMitQueue()
    {
        $subscriptions = JudopaySubscription::getActiveSubscriptions();

        // Get renting booking IDs
        $rentingBookingIds = $subscriptions
            ->where('subscribable_type', RentingBooking::class)
            ->pluck('subscribable_id')
            ->filter();

        // Get finance application IDs
        $financeApplicationIds = $subscriptions
            ->where('subscribable_type', FinanceApplication::class)
            ->pluck('subscribable_id')
            ->filter();

        $rentingInvoices = collect();

        if ($rentingBookingIds->isNotEmpty()) {
            // CURRENT RULE IS ONLY GRAB INVOICES OF THE DISCUSS WEEK.
            $rentingInvoices = BookingInvoice::whereIn('booking_id', $rentingBookingIds)
                ->where('is_posted', true)
                ->where('is_paid', false)
                ->where('invoice_date', '>=', Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->where('invoice_date', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'))
                ->select('id', 'booking_id', 'invoice_date')
                ->get()
                ->map(function($invoice) {
                    $cleanDate = Carbon::parse($invoice->invoice_date)->format('Ymd');
                    return [
                        'id' => $invoice->id,
                        'booking_id' => $invoice->booking_id,
                        'invoice_date' => $cleanDate,
                        'type' => 'RentingBooking'
                    ];
                });
        }

        $financeInvoices = collect();

        if ($financeApplicationIds->isNotEmpty()) {
            // Calculate fixed reference point: Monday 08:00:00 of current week
            // This ensures 100% consistency regardless of when command runs during the week
            $weekReference = Carbon::now()->startOfWeek()->setTime(4, 0, 0);
            $invoiceNumberDate = $weekReference->format('Ymd');
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            // Get Finance subscriptions with their billing info
            $financeSubscriptions = JudopaySubscription::whereIn('subscribable_id', $financeApplicationIds)
                ->where('subscribable_type', FinanceApplication::class)
                ->whereIn('billing_frequency', ['weekly', 'monthly'])
                ->select('subscribable_id', 'billing_frequency', 'billing_day')
                ->get()
                ->keyBy('subscribable_id');

            // Get Finance applications
            $financeApplications = FinanceApplication::whereIn('id', $financeApplicationIds)
                ->where('is_posted', true)
                ->select('id', 'customer_id')
                ->get();

            foreach ($financeApplications as $finance) {
                $subscription = $financeSubscriptions->get($finance->id);

                if (!$subscription) {
                    continue;
                }

                $invoiceDate = null;

                if ($subscription->billing_frequency === 'weekly') {
                    // Weekly subscriptions: invoice on Saturday
                    $invoiceDate = $weekReference->copy()->addDays(5)->format('Ymd'); // Saturday
                } elseif ($subscription->billing_frequency === 'monthly' && $subscription->billing_day) {
                    // Monthly subscriptions: check if billing_day falls in current week
                    // Handle weeks that span months (e.g., week ending Dec 1st)
                    $currentMonth = Carbon::now()->month;
                    $currentYear = Carbon::now()->year;

                    // Check billing_day in current month
                    $billingDate = Carbon::create($currentYear, $currentMonth, $subscription->billing_day);
                    if ($billingDate->between($weekStart, $weekEnd, true)) {
                        $invoiceDate = $billingDate->format('Ymd');
                    } else {
                        // Check billing_day in next month (if week spans months)
                        $nextMonth = Carbon::now()->copy()->addMonth();
                        $billingDateNext = Carbon::create($nextMonth->year, $nextMonth->month, $subscription->billing_day);
                        if ($billingDateNext->between($weekStart, $weekEnd, true)) {
                            $invoiceDate = $billingDateNext->format('Ymd');
                        }
                    }
                }

                // Only create invoice if we have a valid invoice date
                if ($invoiceDate) {
                    $invoiceNumber = $finance->id . '-' . $finance->customer_id . '-' . $invoiceNumberDate;
                    $financeInvoices->push([
                        'id' => $finance->id,
                        'invoice_number' => $invoiceNumber,
                        'invoice_date' => $invoiceDate,
                        'type' => 'FinanceApplication'
                    ]);
                }
            }
        }

        return $rentingInvoices->merge($financeInvoices);
    }
}
