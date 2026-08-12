<?php

namespace App\Services;

use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class RentingInvoiceSyncService
{
    /**
     * Update the booking-level weekly rental price and realign unpaid invoice
     * amounts to that price while preserving each invoice's deposit component.
     *
     * @return array{booking_id: int, weekly_rent: float, items_updated: int, invoices_updated: int, invoices_completed: int}
     */
    public function updateBookingWeeklyRent(int $bookingId, float $weeklyRent): array
    {
        if ($weeklyRent < 0) {
            throw new InvalidArgumentException('Weekly rental price cannot be negative.');
        }

        return DB::transaction(function () use ($bookingId, $weeklyRent): array {
            $booking = RentingBooking::query()
                ->whereKey($bookingId)
                ->lockForUpdate()
                ->firstOrFail();

            $items = RentingBookingItem::query()
                ->where('booking_id', $booking->id)
                ->whereNull('end_date')
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                $latestItem = RentingBookingItem::query()
                    ->where('booking_id', $booking->id)
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if ($latestItem) {
                    $items = collect([$latestItem]);
                }
            }

            if ($items->isEmpty()) {
                throw new RuntimeException('No rental item found for this booking.');
            }

            $itemIds = $items->pluck('id')->all();
            $itemsUpdated = RentingBookingItem::query()
                ->whereIn('id', $itemIds)
                ->update(['weekly_rent' => $weeklyRent]);

            $unpaidInvoices = BookingInvoice::query()
                ->where('booking_id', $booking->id)
                ->where('is_paid', false)
                ->where('amount', '>', 0)
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($unpaidInvoices->isEmpty()) {
                return [
                    'booking_id' => (int) $booking->id,
                    'weekly_rent' => round($weeklyRent, 2),
                    'items_updated' => $itemsUpdated,
                    'invoices_updated' => 0,
                    'invoices_completed' => 0,
                ];
            }

            $invoiceIds = $unpaidInvoices->pluck('id')->all();
            $paidTotals = RentingTransaction::query()
                ->selectRaw('invoice_id, SUM(amount) as paid_total, MAX(transaction_date) as latest_transaction_date')
                ->whereIn('invoice_id', $invoiceIds)
                ->whereNotNull('invoice_id')
                ->groupBy('invoice_id')
                ->get()
                ->keyBy('invoice_id');

            $invoicesUpdated = 0;
            $invoicesCompleted = 0;

            foreach ($unpaidInvoices as $invoice) {
                $targetAmount = $this->invoiceAmountForWeeklyRent($invoice, $weeklyRent);
                $paid = $paidTotals->get($invoice->id);
                $paidTotal = round((float) ($paid?->paid_total ?? 0), 2);

                $payload = [
                    'amount' => $targetAmount,
                    'state' => 'Awaiting Payment',
                ];

                if ($targetAmount <= 0 || $paidTotal >= $targetAmount) {
                    $payload['is_paid'] = true;
                    $payload['paid_date'] = $targetAmount <= 0
                        ? null
                        : ($paid?->latest_transaction_date
                            ? Carbon::parse($paid->latest_transaction_date)->toDateString()
                            : now()->toDateString());
                    $payload['state'] = 'Completed';
                    $payload['notes'] = trim(((string) ($invoice->notes ?? ''))."\nWeekly rent changed; invoice covered by updated price/payment state.");
                    $invoicesCompleted++;
                }

                $invoice->update($payload);
                $invoicesUpdated++;
            }

            return [
                'booking_id' => (int) $booking->id,
                'weekly_rent' => round($weeklyRent, 2),
                'items_updated' => $itemsUpdated,
                'invoices_updated' => $invoicesUpdated,
                'invoices_completed' => $invoicesCompleted,
            ];
        });
    }

    public function invoiceAmountForWeeklyRent(object|array $invoice, float $weeklyRent): float
    {
        $deposit = max((float) data_get($invoice, 'deposit', 0), 0.0);

        return round($weeklyRent + $deposit, 2);
    }

    /**
     * @return array{updated: int, booking_id: int, first_date: string, last_date: string|null}
     */
    public function resequenceUnpaidInvoiceDatesFrom(int $invoiceId, string $firstDate): array
    {
        $firstDate = Carbon::parse($firstDate)->toDateString();

        return DB::transaction(function () use ($invoiceId, $firstDate): array {
            $anchor = BookingInvoice::query()
                ->whereKey($invoiceId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($anchor->is_paid) {
                throw new RuntimeException('Paid invoice dates are locked and cannot be resequenced.');
            }

            $invoices = BookingInvoice::query()
                ->where('booking_id', $anchor->booking_id)
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $plan = $this->buildWeeklyDatePlan($invoices, $invoiceId, $firstDate);
            $this->guardAgainstPreservedDateConflicts($invoices, $plan);

            $moving = $invoices->whereIn('id', array_keys($plan))->values();
            $temporaryDates = $this->temporaryInvoiceDates($moving, $invoices, array_values($plan));

            foreach ($temporaryDates as $movingInvoiceId => $temporaryDate) {
                BookingInvoice::query()
                    ->whereKey($movingInvoiceId)
                    ->update(['invoice_date' => $temporaryDate]);
            }

            foreach ($plan as $movingInvoiceId => $targetDate) {
                BookingInvoice::query()
                    ->whereKey($movingInvoiceId)
                    ->update(['invoice_date' => $targetDate]);
            }

            return [
                'updated' => count($plan),
                'booking_id' => (int) $anchor->booking_id,
                'first_date' => $firstDate,
                'last_date' => array_values($plan)[count($plan) - 1] ?? null,
            ];
        });
    }

    /**
     * @return array{updated: int, booking_id: int, first_date: string, last_date: string|null}
     */
    public function resequenceUnpaidInvoiceDatesForBookingSchedule(int $bookingId, string $startDate): array
    {
        $startDate = Carbon::parse($startDate)->toDateString();

        return DB::transaction(function () use ($bookingId, $startDate): array {
            $invoices = BookingInvoice::query()
                ->where('booking_id', $bookingId)
                ->where('is_posted', true)
                ->where('amount', '>', 0)
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $plan = $this->buildScheduleDatePlan($invoices, $startDate);
            $this->guardAgainstPreservedDateConflicts($invoices, $plan);

            $moving = $invoices->whereIn('id', array_keys($plan))->values();
            $temporaryDates = $this->temporaryInvoiceDates($moving, $invoices, array_values($plan));

            foreach ($temporaryDates as $movingInvoiceId => $temporaryDate) {
                BookingInvoice::query()
                    ->whereKey($movingInvoiceId)
                    ->update(['invoice_date' => $temporaryDate]);
            }

            foreach ($plan as $movingInvoiceId => $targetDate) {
                BookingInvoice::query()
                    ->whereKey($movingInvoiceId)
                    ->update(['invoice_date' => $targetDate]);
            }

            return [
                'updated' => count($plan),
                'booking_id' => $bookingId,
                'first_date' => $startDate,
                'last_date' => array_values($plan)[count($plan) - 1] ?? null,
            ];
        });
    }

    /**
     * @param  iterable<int, BookingInvoice|object|array<string, mixed>>  $bookingInvoices
     * @return array<int, string> invoice id => ISO invoice date
     */
    public function buildWeeklyDatePlan(iterable $bookingInvoices, int $anchorInvoiceId, string $firstDate): array
    {
        $start = Carbon::parse($firstDate)->startOfDay();

        $unpaidInvoices = collect($bookingInvoices)
            ->filter(static fn ($invoice): bool => ! (bool) data_get($invoice, 'is_paid'))
            ->sort(static function ($a, $b): int {
                $dateCompare = strcmp(
                    Carbon::parse(data_get($a, 'invoice_date'))->toDateString(),
                    Carbon::parse(data_get($b, 'invoice_date'))->toDateString()
                );

                return $dateCompare !== 0
                    ? $dateCompare
                    : ((int) data_get($a, 'id') <=> (int) data_get($b, 'id'));
            })
            ->values();

        $anchorIndex = $unpaidInvoices->search(
            static fn ($invoice): bool => (int) data_get($invoice, 'id') === $anchorInvoiceId
        );

        if ($anchorIndex === false) {
            throw new InvalidArgumentException('Only unpaid invoices can be used as the weekly payment anchor.');
        }

        $plan = [];

        foreach ($unpaidInvoices->slice($anchorIndex)->values() as $weekOffset => $invoice) {
            $plan[(int) data_get($invoice, 'id')] = $start->copy()->addWeeks($weekOffset)->toDateString();
        }

        return $plan;
    }

    /**
     * @param  iterable<int, BookingInvoice|object|array<string, mixed>>  $bookingInvoices
     * @return array<int, string> invoice id => ISO invoice date
     */
    public function buildScheduleDatePlan(iterable $bookingInvoices, string $startDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $plan = [];

        $invoices = collect($bookingInvoices)
            ->sort(static function ($a, $b): int {
                $dateCompare = strcmp(
                    Carbon::parse(data_get($a, 'invoice_date'))->toDateString(),
                    Carbon::parse(data_get($b, 'invoice_date'))->toDateString()
                );

                return $dateCompare !== 0
                    ? $dateCompare
                    : ((int) data_get($a, 'id') <=> (int) data_get($b, 'id'));
            })
            ->values();

        foreach ($invoices as $weekOffset => $invoice) {
            if ((bool) data_get($invoice, 'is_paid')) {
                continue;
            }

            $plan[(int) data_get($invoice, 'id')] = $start->copy()->addWeeks($weekOffset)->toDateString();
        }

        return $plan;
    }

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

    /**
     * @param  Collection<int, BookingInvoice>  $invoices
     * @param  array<int, string>  $plan
     */
    private function guardAgainstPreservedDateConflicts(Collection $invoices, array $plan): void
    {
        $movingIds = array_fill_keys(array_keys($plan), true);

        $preservedDates = $invoices
            ->reject(static fn (BookingInvoice $invoice): bool => isset($movingIds[$invoice->id]))
            ->mapWithKeys(static fn (BookingInvoice $invoice): array => [
                Carbon::parse($invoice->invoice_date)->toDateString() => $invoice->id,
            ]);

        foreach ($plan as $targetDate) {
            if ($preservedDates->has($targetDate)) {
                $conflictingInvoiceId = $preservedDates->get($targetDate);

                throw new RuntimeException(
                    "Cannot use {$targetDate}; it is already used by invoice #{$conflictingInvoiceId}. Paid and earlier invoices were not changed."
                );
            }
        }
    }

    /**
     * @param  Collection<int, BookingInvoice>  $moving
     * @param  Collection<int, BookingInvoice>  $allInvoices
     * @param  array<int, string>  $targetDates
     * @return array<int, string>
     */
    private function temporaryInvoiceDates(Collection $moving, Collection $allInvoices, array $targetDates): array
    {
        $blocked = [];

        foreach ($allInvoices as $invoice) {
            $blocked[Carbon::parse($invoice->invoice_date)->toDateString()] = true;
        }

        foreach ($targetDates as $targetDate) {
            $blocked[Carbon::parse($targetDate)->toDateString()] = true;
        }

        $dates = [];
        $cursor = Carbon::create(1901, 1, 1)->startOfDay();

        foreach ($moving as $invoice) {
            while (isset($blocked[$cursor->toDateString()])) {
                $cursor->addDay();
            }

            $date = $cursor->toDateString();
            $dates[(int) $invoice->id] = $date;
            $blocked[$date] = true;
            $cursor->addDay();
        }

        return $dates;
    }
}
