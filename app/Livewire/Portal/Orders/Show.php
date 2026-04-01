<?php

namespace App\Livewire\Portal\Orders;

use App\Models\Ecommerce\EcOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public int $orderId;

    public string $statusMessage = '';

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function cancelOrder(): void
    {
        $customer = Auth::guard('customer')->user();
        $order = EcOrder::query()
            ->where('id', $this->orderId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $currentStatus = strtolower((string) $order->order_status);
        if (in_array($currentStatus, ['cancelled', 'delivered'], true)) {
            $this->statusMessage = 'This order can no longer be cancelled.';

            return;
        }

        $order->order_status = 'Cancelled';
        $order->shipping_status = 'Cancelled';
        $order->save();

        $this->statusMessage = 'Order cancelled successfully.';
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();

        $order = EcOrder::with([
            'items.product',
            'shippingMethod',
            'paymentMethod',
            'customerAddress',
            'branch',
        ])
            ->where('id', $this->orderId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        return view('livewire.portal.orders.show', compact('order'))
            ->layout('components.layouts.portal', [
                'title' => 'Order #'.$order->id.' | My Account',
            ]);
    }
}
