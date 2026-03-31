<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingInternal extends Mailable
{
    use Queueable, SerializesModels;

    /** @var ServiceBooking */
    public $booking;

    public function __construct(ServiceBooking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Service Enquiry Received')
            ->view('olders.emails.service-booking-internal')
            ->with([
                'booking' => $this->booking,
            ]);
    }
}
