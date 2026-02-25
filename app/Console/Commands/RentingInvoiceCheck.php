<?php

namespace App\Console\Commands;

use App\Mail\RentInvoiceReminderMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // This needs to be created
use Illuminate\Support\Facades\Mail;

class RentingInvoiceCheck extends Command
{
    protected $signature = 'app:renting-invoice-check';

    protected $description = 'Renting Pending Invoice Notification';

    public function handle()
    {
        $bookings = DB::select("
            SELECT rb.customer_id,
                   (SELECT concat(first_name,' ',last_name) FROM customers WHERE customers.id = rb.customer_id) as fullname,
                   (SELECT phone FROM customers WHERE customers.id = rb.customer_id) as phone,
                   (SELECT email FROM customers WHERE customers.id = rb.customer_id) as email,
                   rb.start_date as contract_date,
                   rbi.motorbike_id,
                   (SELECT reg_no FROM motorbikes WHERE id = rbi.motorbike_id) as reg_no,
                   rbi.weekly_rent,
                   DAYOFWEEK(rb.start_date) as start_day,
                   (rb.start_date) as start_date
            FROM renting_bookings rb
            INNER JOIN renting_booking_items rbi ON rbi.booking_id = rb.id
            WHERE rb.is_posted = true AND rbi.end_date IS NULL
            ORDER BY rb.id, rb.start_date
        ");

        foreach ($bookings as $booking) {

            $notificationDay = Carbon::parse($booking->contract_date)->subDay()->dayOfWeekIso;

            $this->info('Sent instalment notification to: '.$notificationDay);

            if (Carbon::now()->dayOfWeekIso === $notificationDay) {

                $this->info('Sent instalment notification to: ');

                if (! $this->shouldIgnoreEmail($booking->email)) {
                    try {
                        $email = new RentInvoiceReminderMail($booking);
                        Mail::to($booking->email)
                            // ->cc('customerservice@neguinhomotors.co.uk')
                            ->send($email);
                        $this->info('Sent rent invoice reminder to: '.$booking->fullname);
                    } catch (\Exception $e) {
                        $this->error('Failed to send email to: '.$booking->email.' | Error: '.$e->getMessage());
                    }
                } else {
                    $this->error('Ignoring email: '.$booking->email);
                }
            }
        }
    }

    protected function shouldIgnoreEmail($email)
    {
        $patterns = [
            '/\d+no@/',
            '/email\.ngm$/',
            '/email\.com-$/',
            '/\d+@/',
            '/-[a-zA-Z0-9]+@/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return true;
            }
        }

        return false;
    }
}
