<?php

namespace App\Mail\Ecommerce;

use App\Models\Ecommerce\EcOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRefundMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $customerAuth;

    public function __construct(EcOrder $order)
    {
        if (! $order->relationLoaded('items')) {
            $order->load('items');
        }
        if (! $order->relationLoaded('customer')) {
            $order->load('customer');
        }

        $this->order = $order;
        $this->customerAuth = $order->customerAuth;
    }

    public function build()
    {
        return $this->view('emails.ecommerce.order-refund')
            ->subject('ORDER #'.$this->order->id.' REFUNDED')
            ->with([
                'order' => $this->order,
                'customer' => $this->customerAuth->customer,
                'items' => $this->order->items,
            ]);
    }
}
