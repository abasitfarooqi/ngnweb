<?php

namespace App\Console\Commands;

use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingTransaction;
use App\Services\RentingInvoiceSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RentingInvoiceCleanupHistorical extends Command
{
    protected $signature = 'app:renting-invoice-cleanup-historical
                            {--dry-run : Report only, do not delete or create}
                            {--sync-active : Run future sync for all active bookings}
                            {--historical : Also remove past unpaid wrong-weekday invoices with no transactions}';

    protected $description = 'One-time cleanup for renting invoice duplicates (run before unique index migration)';

    public function handle(RentingInvoiceSyncService $syncService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->reportDuplicateDates($dryRun);

        if ($this->option('sync-active')) {
            $this->syncActiveBookings($syncService, $dryRun);
        }

        if ($this->option('historical')) {
            $this->cleanupHistoricalWrongWeekday($dryRun);
        }

        if (! $this->option('sync-active') && ! $this->option('historical')) {
            $this->line('No actions selected. Use --sync-active and/or --historical (optional --dry-run).');
        }

        return self::SUCCESS;
    }

    protected function reportDuplicateDates(bool $dryRun): void
    {
        $duplicates = DB::table('booking_invoices')
            ->select('booking_id', 'invoice_date', DB::raw('COUNT(*) as row_count'))
            ->groupBy('booking_id', 'invoice_date')
            ->having('row_count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate (booking_id, invoice_date) pairs found.');

            return;
        }

        $this->warn("Found {$duplicates->count()} duplicate (booking_id, invoice_date) pair(s):");
        foreach ($duplicates as $row) {
            $this->line("  booking_id={$row->booking_id} invoice_date={$row->invoice_date} count={$row->row_count}");
        }

        if ($dryRun) {
            $this->line('Dry run: resolve duplicates manually before running the unique index migration.');
        }
    }

    protected function syncActiveBookings(RentingInvoiceSyncService $syncService, bool $dryRun): void
    {
        $bookingIds = $syncService->getActiveBookingIds();
        $totalDeleted = 0;
        $totalCreated = 0;

        foreach ($bookingIds as $bookingId) {
            if ($dryRun) {
                $this->line("Would sync future invoices for booking {$bookingId}");

                continue;
            }

            $result = $syncService->syncFutureInvoicesForBooking($bookingId);
            if ($result['skipped']) {
                continue;
            }

            $totalDeleted += $result['deleted'];
            $totalCreated += $result['created'];
        }

        $this->info("Future sync complete: {$totalDeleted} deleted, {$totalCreated} created across ".count($bookingIds).' active booking(s).');
    }

    protected function cleanupHistoricalWrongWeekday(bool $dryRun): void
    {
        $deleted = 0;
        $bookings = RentingBooking::query()
            ->whereNotNull('start_date')
            ->get(['id', 'start_date']);

        foreach ($bookings as $booking) {
            $validWeekday = Carbon::parse($booking->start_date)->dayOfWeek;
            $today = Carbon::today()->toDateString();

            $invoiceIdsWithTransactions = RentingTransaction::query()
                ->where('booking_id', $booking->id)
                ->whereNotNull('invoice_id')
                ->pluck('invoice_id')
                ->unique()
                ->all();

            $candidates = BookingInvoice::query()
                ->where('booking_id', $booking->id)
                ->where('is_paid', false)
                ->whereNull('paid_date')
                ->whereDate('invoice_date', '<', $today)
                ->whereNotIn('id', $invoiceIdsWithTransactions ?: [0])
                ->get();

            foreach ($candidates as $invoice) {
                if (Carbon::parse($invoice->invoice_date)->dayOfWeek === $validWeekday) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("Would delete historical invoice #{$invoice->id} (booking {$booking->id}, date {$invoice->invoice_date})");

                    continue;
                }

                $invoice->delete();
                $deleted++;
            }
        }

        $this->info($dryRun
            ? 'Dry run: historical wrong-weekday scan complete.'
            : "Historical cleanup: {$deleted} past wrong-weekday invoice(s) removed.");
    }
}
