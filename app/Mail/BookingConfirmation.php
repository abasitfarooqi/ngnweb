<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(ServiceBooking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Service Enquiry Received')
            ->view('olders.emails.service-booking-confirmation')
            ->with([
                'booking' => $this->booking,
            ]);
    }
}
