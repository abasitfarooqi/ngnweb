<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorbikeTransportDeliveryOrderEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('Motorbike transport delivery enquiry')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.motorbike_transport_delivery_order_enquiry',
                ['order' => (object) $this->emailData],
                'Motorbike transport delivery enquiry',
            ));
    }
}
