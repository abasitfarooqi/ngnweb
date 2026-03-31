<?php

namespace App\Mail;

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
        return $this->view('olders.emails.motorbike_transport_delivery_order_confirmed')
            ->with(['order' => $this->order]);
    }
}
