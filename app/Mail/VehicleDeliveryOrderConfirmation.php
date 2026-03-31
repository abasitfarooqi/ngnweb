<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VehicleDeliveryOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user and order data for the email.
     */
    public $user;

    public $order;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $order  The order data
     * @param  mixed|null  $user  The user data (optional)
     */
    public function __construct($order, $user = null)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('olders.emails.vehicle_delivery_order_confirmation')
            ->subject('Pickup Estimate');
    }
}
