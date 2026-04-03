<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorbikeTransportDeliveryOrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // Template Directory: emails, Template Name: motorbike_transport_delivery_order_cancelled
    public function build()
    {
        return $this->subject('Motorbike delivery order cancelled')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.motorbike_transport_delivery_order_cancelled',
                ['order' => $this->order],
                'Motorbike delivery order cancelled',
            ));
    }
}
