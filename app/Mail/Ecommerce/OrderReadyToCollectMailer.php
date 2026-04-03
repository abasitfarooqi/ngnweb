<?php

namespace App\Mail\Ecommerce;

use App\Models\Ecommerce\EcOrder;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReadyToCollectMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $customer;

    public function __construct(EcOrder $order)
    {
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
        $title = 'ORDER #'.$this->order->id.' READY TO COLLECT';

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.order-ready-to-collect',
                [
                    'order' => $this->order,
                    'customer' => $this->customer,
                    'items' => $this->order->items()->with('product')->get(),
                    'shipping' => $this->order->shipping,
                    'address' => $this->order->customerAddress,
                    'shippingMethod' => $this->order->shippingMethod,
                    'branch' => $this->order->branch,
                    'status' => $this->order->order_status,
                ],
                $title,
            ));
    }
}
