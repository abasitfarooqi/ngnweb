<?php

namespace App\Console\Commands;

use App\Mail\InvoiceGenerationNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RentingInvoiceGenerate extends Command
{
    protected $signature = 'app:renting-invoice-generate';

    protected $description = 'Renting Invoice Generation';

    public function handle()
    {
        $bookingIds = DB::table('renting_booking_items')
            ->select('booking_id')
            ->distinct()
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->pluck('booking_id')
            ->toArray();

        $newInvoices = 0;

        foreach ($bookingIds as $bookingId) {
            // Get the weekly rent
            $weeklyRent = DB::table('renting_booking_items')
                ->where('booking_id', $bookingId)
                ->value('weekly_rent');

            // Get the booking date
            $bookingDate = DB::table('renting_bookings')
                ->where('id', $bookingId)
                ->value('start_date');

            // Get the day of the week
            $dayOfWeek = Carbon::parse($bookingDate)->dayOfWeek;

            // Get the invoice dates
            $invoiceDates = [
                Carbon::now()->next($dayOfWeek),
                Carbon::now()->next($dayOfWeek)->addWeek(),
                Carbon::now()->next($dayOfWeek)->addWeeks(2),
            ];

            // Loop through the invoice dates
            foreach ($invoiceDates as $invoiceDate) {
                // Check if the invoice already exists
                $exists = \App\Models\BookingInvoice::where('booking_id', $bookingId)
                    ->whereDate('invoice_date', $invoiceDate->format('Y-m-d'))
                    ->exists();

                // If the invoice does not exist, create it
                if (! $exists) {
                    \App\Models\BookingInvoice::create([
                        'booking_id' => $bookingId,
                        'user_id' => 93,
                        'invoice_date' => $invoiceDate,
                        'amount' => $weeklyRent,
                        'deposit' => 0.00,
                        'is_posted' => true,
                        'is_paid' => false,
                        'state' => 'Awaiting Payment',
                        'notes' => 'Invoice created as unpaid',
                    ]);
                    $newInvoices++;
                }
            }
        }

        // Prepare data for email
        $data = [
            'email' => ['support@neguinhomotors.co.uk'],
            'totalProcessed' => count($bookingIds),
            'newInvoices' => $newInvoices,
        ];

        if ($newInvoices > 0) {
            Mail::to($data['email'])->send(new InvoiceGenerationNotification($data));
        }

        $this->info("{$newInvoices} new invoices generated.");
    }
}
