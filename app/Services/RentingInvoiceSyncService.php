<?php

namespace App\Services;

use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentingInvoiceSyncService
{
    /**
     * @return array{deleted: int, created: int, kept: int, skipped: bool}
     */
    public function syncFutureInvoicesForBooking(int $bookingId): array
    {
        $result = [
            'deleted' => 0,
            'created' => 0,
            'kept' => 0,
            'skipped' => false,
        ];

        $booking = RentingBooking::find($bookingId);
        if (! $booking || ! $booking->start_date) {
            $result['skipped'] = true;

            return $result;
        }

        $bookingItem = RentingBookingItem::query()
            ->where('booking_id', $bookingId)
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->first();

        if (! $bookingItem || (float) $bookingItem->weekly_rent <= 0) {
            $result['skipped'] = true;

            return $result;
        }

        $validWeekday = Carbon::parse($booking->start_date)->dayOfWeek;
        $targetDates = $this->computeTargetDates($validWeekday);
        $targetDateStrings = array_map(
            static fn (Carbon $date) => $date->toDateString(),
            $targetDates
        );
        $today = Carbon::today()->toDateString();

        DB::transaction(function () use (
            $bookingId,
            $booking,
            $bookingItem,
            $validWeekday,
            $targetDateStrings,
            $targetDates,
            $today,
            &$result
        ) {
            $invoiceIdsWithTransactions = RentingTransaction::query()
                ->where('booking_id', $bookingId)
                ->whereNotNull('invoice_id')
                ->pluck('invoice_id')
                ->unique()
                ->all();

            $invoices = BookingInvoice::query()
                ->where('booking_id', $bookingId)
                ->get();

            foreach ($invoices as $invoice) {
                if (! $this->isFutureDeletable($invoice, $today, $invoiceIdsWithTransactions)) {
                    $invoiceDateStr = Carbon::parse($invoice->invoice_date)->toDateString();
                    if ($invoiceDateStr >= $today && in_array($invoiceDateStr, $targetDateStrings, true)) {
                        $result['kept']++;
                    }

                    continue;
                }

                if ($this->shouldDeleteFutureInvoice($invoice, $validWeekday, $targetDateStrings)) {
                    $invoice->delete();
                    $result['deleted']++;
                }
            }

            foreach ($targetDates as $targetDate) {
                $dateStr = $targetDate->toDateString();

                $exists = BookingInvoice::query()
                    ->where('booking_id', $bookingId)
                    ->whereDate('invoice_date', $dateStr)
                    ->exists();

                if ($exists) {
                    continue;
                }

                BookingInvoice::create([
                    'booking_id' => $bookingId,
                    'user_id' => $booking->user_id,
                    'invoice_date' => $dateStr,
                    'amount' => $bookingItem->weekly_rent,
                    'deposit' => 0,
                    'is_posted' => true,
                    'is_paid' => false,
                    'state' => 'Awaiting Payment',
                    'notes' => 'Invoice created as unpaid',
                ]);

                $result['created']++;
            }
        });

        return $result;
    }

    /**
     * Next three weekly dates on validWeekday from today (inclusive if today matches).
     *
     * @return array<int, Carbon>
     */
    public function computeTargetDates(int $validWeekday, ?Carbon $fromDate = null): array
    {
        $cursor = ($fromDate ?? Carbon::today())->copy()->startOfDay();

        if ($cursor->dayOfWeek !== $validWeekday) {
            $cursor->next($validWeekday);
        }

        return [
            $cursor->copy(),
            $cursor->copy()->addWeek(),
            $cursor->copy()->addWeeks(2),
        ];
    }

    public function shouldDeleteFutureInvoice(
        BookingInvoice $invoice,
        int $validWeekday,
        array $targetDateStrings
    ): bool {
        $invoiceDate = Carbon::parse($invoice->invoice_date);
        $wrongWeekday = $invoiceDate->dayOfWeek !== $validWeekday;
        $notInTargetSet = ! $wrongWeekday
            && ! in_array($invoiceDate->toDateString(), $targetDateStrings, true);

        return $wrongWeekday || $notInTargetSet;
    }

    /**
     * @return array<int, int>
     */
    public function getActiveBookingIds(): array
    {
        return DB::table('renting_booking_items')
            ->select('booking_id')
            ->distinct()
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->pluck('booking_id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @param  array<int, int|string>  $invoiceIdsWithTransactions
     */
    protected function isFutureDeletable(BookingInvoice $invoice, string $today, array $invoiceIdsWithTransactions): bool
    {
        if ($invoice->is_paid) {
            return false;
        }

        if ($invoice->paid_date !== null) {
            return false;
        }

        if (in_array($invoice->id, $invoiceIdsWithTransactions, true)) {
            return false;
        }

        if (Carbon::parse($invoice->invoice_date)->toDateString() < $today) {
            return false;
        }

        return true;
    }
}
