<?php

namespace App\Mail\Ecommerce;

use App\Models\Ecommerce\EcOrder;
use App\Support\UniversalMailPayload;
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
        $title = 'ORDER #'.$this->order->id.' REFUNDED';

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.order-refund',
                [
                    'order' => $this->order,
                    'customer' => $this->customerAuth->customer,
                    'items' => $this->order->items,
                ],
                $title,
            ));
    }
}
