<?php

namespace App\Http\Controllers\UserPanel;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class NgnOrderController extends Controller
{
    public function __construct()
    {
        // Apply the 'auth:customer' middleware to ensure only authenticated customers can access these methods
        $this->middleware('auth:customer');
    }

    public function index()
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        // Fetch orders related to the authenticated customer
        $orders = Order::where('customer_id', $customer->customer_id)->get();

        return view('livewire.agreements.migrated.frontend.ngnstore.user_panel.orders.index', compact('orders'));
    }

    public function show($id)
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        // Find the order ensuring it belongs to the authenticated customer
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->customer_id)
            ->firstOrFail();

        return view('livewire.agreements.migrated.frontend.ngnstore.user_panel.orders.details', compact('order'));
    }

    public function tracking($id)
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        // Find the order ensuring it belongs to the authenticated customer
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->customer_id)
            ->firstOrFail();

        return view('livewire.agreements.migrated.frontend.ngnstore.user_panel.orders.tracking', compact('order'));
    }

    // New method to display the confirmation page
    public function confirm($id)
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        // Find the order ensuring it belongs to the authenticated customer
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->customer_id)
            ->firstOrFail();

        return view('livewire.agreements.migrated.frontend.ngnstore.user_panel.orders.confirm', compact('order'));
    }

    public function confirmOrder($orderId)
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customer->customer_id)
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Order cannot be confirmed.');
        }

        // TODO: Deduct stock logic here...

        $order->status = 'confirmed';
        $order->save();

        return redirect()->route('userpanel_order_details', $orderId)->with('success', 'Order confirmed successfully.');
    }
}
