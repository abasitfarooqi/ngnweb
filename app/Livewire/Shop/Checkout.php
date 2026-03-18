<?php

namespace App\Livewire\Shop;

use App\Models\CustomerAddress;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcOrderItem;
use App\Models\Ecommerce\EcOrderShipping;
use App\Models\Ecommerce\EcPaymentMethod;
use App\Models\Ecommerce\EcShippingMethod;
use App\Models\NgnProduct;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    public int $step = 1;

    // Step 1: Address
    public int $selectedAddressId = 0;
    public bool $newAddress = false;
    public string $first_name = '';
    public string $last_name = '';
    public string $company_name = '';
    public string $street_address = '';
    public string $street_address_plus = '';
    public string $postcode = '';
    public string $city = '';
    public string $phone_number = '';

    // Step 2: Shipping
    public int $shippingMethodId = 0;
    public int $branchId = 0;

    // Step 3: Review / Payment
    public int $paymentMethodId = 0;

    // Result
    public ?int $orderId = null;
    public string $errorMessage = '';

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function mount(): void
    {
        if (!Auth::guard('customer')->check()) {
            $this->redirectRoute('login');
            return;
        }

        if ($this->cart->isEmpty()) {
            $this->redirectRoute('shop.basket');
            return;
        }

        $customer = Auth::guard('customer')->user();
        $defaultAddress = CustomerAddress::where('customer_id', $customer->customer_id)
            ->where('is_default', true)
            ->first();

        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }

        $defaultShipping = EcShippingMethod::active()->first();
        if ($defaultShipping) {
            $this->shippingMethodId = $defaultShipping->id;
        }

        $defaultPayment = EcPaymentMethod::active()->first();
        if ($defaultPayment) {
            $this->paymentMethodId = $defaultPayment->id;
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateAddress();
        } elseif ($this->step === 2) {
            $this->validateShipping();
        }
        $this->step++;
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function validateAddress(): void
    {
        if ($this->newAddress) {
            $this->validate([
                'first_name'    => 'required|string|max:100',
                'last_name'     => 'required|string|max:100',
                'street_address'=> 'required|string|max:255',
                'postcode'      => 'required|string|max:20',
                'city'          => 'required|string|max:100',
                'phone_number'  => 'required|string|max:30',
            ]);
        } else {
            $this->validate(['selectedAddressId' => 'required|integer|min:1']);
        }
    }

    protected function validateShipping(): void
    {
        $this->validate(['shippingMethodId' => 'required|integer|min:1']);
    }

    public function placeOrder(): void
    {
        $this->errorMessage = '';

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            $this->errorMessage = 'Please sign in to place an order.';
            return;
        }

        $items = $this->cart->getItems();
        if (empty($items)) {
            $this->errorMessage = 'Your basket is empty.';
            return;
        }

        $shippingMethod = EcShippingMethod::find($this->shippingMethodId);
        if (!$shippingMethod) {
            $this->errorMessage = 'Please select a shipping method.';
            return;
        }

        try {
            DB::beginTransaction();

            // Resolve or create address
            if ($this->newAddress) {
                $address = CustomerAddress::create([
                    'customer_id'         => $customer->customer_id,
                    'first_name'          => $this->first_name,
                    'last_name'           => $this->last_name,
                    'company_name'        => $this->company_name ?: '-',
                    'street_address'      => $this->street_address,
                    'street_address_plus' => $this->street_address_plus ?: '-',
                    'postcode'            => $this->postcode,
                    'city'                => $this->city,
                    'phone_number'        => $this->phone_number,
                    'is_default'          => false,
                    'type'                => 'shipping',
                    'country_id'          => 3,
                ]);
            } else {
                $address = CustomerAddress::find($this->selectedAddressId);
                if (!$address || $address->customer_id !== $customer->customer_id) {
                    throw new \RuntimeException('Invalid delivery address.');
                }
            }

            $productIds = array_column($items, 'product_id');
            $products   = NgnProduct::whereIn('id', $productIds)->get()->keyBy('id');

            $totalAmount  = array_sum(array_column($items, 'line_total'));
            $shippingCost = (float) $shippingMethod->shipping_amount;
            $grandTotal   = $totalAmount + $shippingCost;

            // Remove any stale pending order
            EcOrder::where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->delete();

            $order = EcOrder::create([
                'customer_id'         => $customer->id,
                'shipping_method_id'  => $this->shippingMethodId,
                'payment_method_id'   => $this->paymentMethodId ?: 3,
                'customer_address_id' => $address->id,
                'branch_id'           => $shippingMethod->in_store_pickup ? ($this->branchId ?: null) : null,
                'order_status'        => 'Confirmed',
                'shipping_status'     => 'pending',
                'payment_status'      => 'pending',
                'shipping_cost'       => $shippingCost,
                'total_amount'        => $totalAmount,
                'tax'                 => 0,
                'discount'            => 0,
                'grand_total'         => $grandTotal,
                'currency'            => 'GBP',
                'order_date'          => now(),
            ]);

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                EcOrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $product?->name ?? $item['product_name'],
                    'sku'          => $product?->sku ?? $item['sku'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total_price'  => $item['line_total'],
                    'tax'          => 0,
                    'discount'     => 0,
                    'line_total'   => $item['line_total'],
                ]);
            }

            EcOrderShipping::create([
                'order_id'          => $order->id,
                'fulfillment_method'=> $shippingMethod->in_store_pickup ? 'pickup' : 'carrier',
                'status'            => 'processing',
                'notes'             => null,
                'processing_at'     => now(),
            ]);

            DB::commit();

            $this->cart->clear();
            $this->orderId = $order->id;
            $this->step    = 4;
            $this->dispatch('cart-updated', count: 0);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed', ['error' => $e->getMessage()]);
            $this->errorMessage = 'Something went wrong. Please try again.';
        }
    }

    public function render()
    {
        $customer       = Auth::guard('customer')->user();
        $addresses      = $customer
            ? CustomerAddress::where('customer_id', $customer->customer_id)->get()
            : collect();
        $shippingMethods = EcShippingMethod::active()->get();
        $paymentMethods  = EcPaymentMethod::active()->get();
        $items           = $this->cart->getItems();
        $subtotal        = $this->cart->subtotal();
        $shippingMethod  = $this->shippingMethodId
            ? EcShippingMethod::find($this->shippingMethodId)
            : null;
        $shippingCost = $shippingMethod ? (float) $shippingMethod->shipping_amount : 0.0;
        $grandTotal   = $subtotal + $shippingCost;

        return view('livewire.shop.checkout', compact(
            'addresses', 'shippingMethods', 'paymentMethods',
            'items', 'subtotal', 'shippingCost', 'grandTotal', 'shippingMethod'
        ))->layout('components.layouts.public', [
            'title'       => 'Checkout | NGN Shop',
            'description' => 'Complete your order at NGN Motors.',
        ]);
    }
}
