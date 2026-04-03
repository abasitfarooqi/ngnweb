<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use App\Support\UniversalMailPayload;
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
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.service-booking-internal',
                ['booking' => $this->booking],
                'Service Enquiry Received',
            ));
    }
}
