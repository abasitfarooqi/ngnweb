<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorbikeDeliveryOrderEnquiryInternal extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->view('olders.emails.motorbike_delivery_order_enquiry_internal')
            ->with('order', (object) $this->emailData);
    }
}
