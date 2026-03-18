<?php

namespace App\Livewire\Portal\Orders;

use App\Models\Ecommerce\EcOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = Auth::guard('customer')->user();

        $orders = EcOrder::with(['items', 'shippingMethod', 'paymentMethod'])
            ->where('customer_id', $customer->id)
            ->where('order_status', '!=', 'pending')
            ->latest()
            ->paginate(10);

        return view('livewire.portal.orders.index', compact('orders'))
            ->layout('components.layouts.portal', [
                'title' => 'My Orders | My Account',
            ]);
    }
}
