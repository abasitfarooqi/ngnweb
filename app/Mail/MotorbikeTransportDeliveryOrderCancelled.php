<?php

namespace App\Mail;

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
        return $this->view('olders.emails.motorbike_transport_delivery_order_cancelled')
            ->with(['order' => $this->order]);
    }
}
