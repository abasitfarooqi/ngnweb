<?php

namespace App\Livewire\Portal\Orders;

use App\Models\Ecommerce\EcOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public int $orderId;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
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
                'title' => 'Order #' . $order->id . ' | My Account',
            ]);
    }
}
