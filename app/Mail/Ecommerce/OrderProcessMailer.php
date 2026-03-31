<?php

namespace App\Mail\Ecommerce;

use App\Models\Ecommerce\EcOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderProcessMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $customer;

    public function __construct(EcOrder $order)
    {
        // Load the necessary relationships if they haven't been loaded
        if (! $order->relationLoaded('shippingMethod')) {
            $order->load('shippingMethod');
        }
        if (! $order->relationLoaded('branch')) {
            $order->load('branch');
        }
        if (! $order->relationLoaded('customerAddress')) {
            $order->load('customerAddress');
        }

        $this->order = $order;
        $this->customer = $order->customer;
    }

    public function build()
    {
        return $this->view('olders.emails.ecommerce.order-process')
            ->subject('Your Order #'.$this->order->id.' is Being Processed!')
            ->with([
                'order' => $this->order,
                'customer' => $this->customer,
                'items' => $this->order->items()->with('product')->get(),
                'shipping' => $this->order->shipping,
                'address' => $this->order->customerAddress,
                'shippingMethod' => $this->order->shippingMethod,
                'branch' => $this->order->branch,
            ]);
    }
}
