<?php

namespace App\Mail\Ecommerce;

use App\Models\Ecommerce\EcOrder;
use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedAlertMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $customer;

    public function __construct(EcOrder $order)
    {
        \Log::info('OrderConfirmedAlertMailer constructor', ['order_id' => $order->id]);
        if (! $order->relationLoaded('shippingMethod')) {
            $order->load('shippingMethod');
        }
        if (! $order->relationLoaded('branch')) {
            $order->load('branch');
        }
        if (! $order->relationLoaded('customerAddress')) {
            $order->load('customerAddress');
        }
        if (! $order->relationLoaded('customer')) {
            $order->load('customer.customer');
        }

        // Load items with their products
        $order->load('items.product');

        $this->order = $order;
        $this->customer = $order->customer->customer;
    }

    public function build()
    {
        $items = $this->order->items()->with('product')->get()->map(function ($item) {
            $item->stock_location = $item->product ? $item->product->getStockLocationText() : 'Check Stock';

            return $item;
        });

        $title = 'ORDER #'.$this->order->id.' CONFIRMED';

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.order-confirmed-alert',
                [
                    'order' => $this->order,
                    'customer' => $this->customer,
                    'items' => $items,
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
