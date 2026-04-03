<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorbikeTransportDeliveryOrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // Template Directory: emails, Template Name: motorbike_transport_delivery_order_confirmd
    public function build()
    {
        return $this->subject('Motorbike delivery order confirmed')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.motorbike_transport_delivery_order_confirmed',
                ['order' => $this->order],
                'Motorbike delivery order confirmed',
            ));
    }
}
