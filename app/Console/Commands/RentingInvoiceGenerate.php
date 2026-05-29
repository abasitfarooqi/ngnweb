<?php

namespace App\Console\Commands;

use App\Mail\InvoiceGenerationNotification;
use App\Services\RentingInvoiceSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RentingInvoiceGenerate extends Command
{
    protected $signature = 'app:renting-invoice-generate';

    protected $description = 'Renting Invoice Generation';

    public function handle(RentingInvoiceSyncService $syncService): int
    {
        $bookingIds = $syncService->getActiveBookingIds();
        $newInvoices = 0;
        $totalDeleted = 0;

        foreach ($bookingIds as $bookingId) {
            $result = $syncService->syncFutureInvoicesForBooking($bookingId);

            if ($result['skipped']) {
                continue;
            }

            $newInvoices += $result['created'];
            $totalDeleted += $result['deleted'];
        }

        $data = [
            'email' => ['support@neguinhomotors.co.uk'],
            'totalProcessed' => count($bookingIds),
            'newInvoices' => $newInvoices,
        ];

        if ($newInvoices > 0) {
            Mail::to($data['email'])->send(new InvoiceGenerationNotification($data));
        }

        $this->info("{$newInvoices} new invoices generated. {$totalDeleted} invalid future invoice(s) removed.");

        return self::SUCCESS;
    }
}
