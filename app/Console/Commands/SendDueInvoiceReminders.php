<?php

namespace App\Console\Commands;

use App\Mail\DueInvoiceCustomerMail;
use App\Mail\DueInvoiceSummaryMail;
use App\Models\BookingInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDueInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:due-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email reminder to customers for invoices due today and a summary to our internal team.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Fetch invoices due today.
        $today = Carbon::today();

        // We look for invoices with invoice_date = today, which is not paid, and have not been sent a reminder yet.
        $dueInvoices = BookingInvoice::whereDate('invoice_date', $today)
            ->whereNull('notified_at')
            ->where('is_paid', false)
            ->with([
                'booking.customer',
                'booking.rentingBookingItems.motorbike',
            ])
            ->get();

        // Filter out invoices with missing relationships
        $dueInvoices = $dueInvoices->filter(function ($invoice) {
            $booking = $invoice->booking;
            if (! $booking) {
                return false;
            }
            if (! $booking->customer) {
                return false;
            }
            // Only consider bookings with at least one active item (end_date is null)
            $activeItem = $booking->rentingBookingItems->first(function ($item) {
                return is_null($item->end_date) && $item->motorbike;
            });

            return $activeItem !== null;
        });

        // If none found, just exit.
        if ($dueInvoices->isEmpty()) {
            $this->info('No invoices due today.');

            return Command::SUCCESS;
        }

        $emailDataList = $dueInvoices->map(function ($invoice) {
            $booking = $invoice->booking;
            // Use the first active item
            $item = $booking->rentingBookingItems->first(function ($item) {
                return is_null($item->end_date) && $item->motorbike;
            });
            $motorbike = $item->motorbike;
            $customer = $booking->customer;

            return [
                'booking_no' => $booking->id,
                'customer_name' => $customer->first_name.' '.$customer->last_name,
                'customer_email' => $customer->email,
                'vin_number' => $motorbike->vin_number,
                'registration_number' => $motorbike->reg_no,
                'weekly_rent' => $item->weekly_rent,
                'invoice_date' => $invoice->invoice_date,
            ];
        });

        // 2. Send individual reminders to each customer.
        foreach ($emailDataList as $emailData) {
            $invoice = $dueInvoices->first(function ($inv) use ($emailData) {
                return $inv->booking->id == $emailData['booking_no'];
            });
            // Double-check: Only send if booking still has at least one active item
            $booking = $invoice ? $invoice->booking : null;
            $hasActiveItem = $booking && $booking->rentingBookingItems->contains(function ($item) {
                return is_null($item->end_date);
            });
            if ($hasActiveItem) {
                Mail::to($emailData['customer_email'])->send(new DueInvoiceCustomerMail($emailData));
                // Mark the invoice as notified.
                $invoice->notified_at = now();
                $invoice->save();
            }
        }

        // 3. Send summary email to internal team.
        $internalRecipients = [
            'customerservice@neguinhomotors.co.uk',
        ];
        Mail::to($internalRecipients)->send(new DueInvoiceSummaryMail($emailDataList));

        $this->info('Reminders sent successfully.');

        return Command::SUCCESS;
    }
}
