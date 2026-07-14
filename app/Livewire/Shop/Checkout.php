<?php

namespace App\Livewire\Shop;

use App\Models\Branch;
use App\Models\CustomerAddress;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcOrderItem;
use App\Models\Ecommerce\EcOrderShipping;
use App\Models\Ecommerce\EcPaymentMethod;
use App\Models\Ecommerce\EcShippingMethod;
use App\Models\NgnProduct;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Builder;
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

    // PayPal return state
    public string $paymentResult = '';

    public string $paymentMessage = '';

    public ?string $transactionId = null;

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function mount(): void
    {
        if (! Auth::guard('customer')->check()) {
            $this->redirectRoute('login');

            return;
        }

        $customer = Auth::guard('customer')->user();

        // Returning from PayPal (PayPalController flashes payment_status to the session).
        if (session()->has('payment_status')) {
            $this->paymentResult = (string) session('payment_status');
            $this->transactionId = session('transaction_id');
            $this->paymentMessage = (string) session('message', '');

            if ($this->paymentResult === 'success') {
                // Payment is confirmed — now it is safe to clear the basket and show confirmation.
                $this->cart->clear();
                $this->dispatch('cart-updated', count: 0)->to('site.header');

                $paidOrder = EcOrder::where('customer_id', $customer->id)
                    ->where('payment_status', 'paid')
                    ->latest('id')
                    ->first();
                $this->orderId = $paidOrder?->id;
                $this->step = 4;

                return;
            }

            // Cancelled / error: the basket is intact so the customer can retry.
            $this->errorMessage = $this->paymentMessage ?: ($this->paymentResult === 'cancelled'
                ? 'Your PayPal payment was cancelled. Your basket is saved — you can try again.'
                : 'Your payment could not be completed. Please try again.');
        }

        if ($this->cart->isEmpty()) {
            $this->redirectRoute('shop.basket');

            return;
        }

        $defaultAddress = CustomerAddress::where('customer_id', $customer->customer_id)
            ->where('is_default', true)
            ->first();

        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }

        // Prefer home delivery by default; fall back to whatever is enabled.
        $defaultShipping = EcShippingMethod::active()->delivery()->first()
            ?? EcShippingMethod::active()->first();
        if ($defaultShipping) {
            $this->shippingMethodId = $defaultShipping->id;
        }

        $defaultPayment = $this->checkoutPaymentMethodsQuery()->first();
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
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'street_address' => 'required|string|max:255',
                'postcode' => 'required|string|max:20',
                'city' => 'required|string|max:100',
                'phone_number' => 'required|string|max:30',
            ]);
        } else {
            $this->validate(['selectedAddressId' => 'required|integer|min:1']);
        }
    }

    protected function validateShipping(): void
    {
        $this->validate(['shippingMethodId' => 'required|integer|min:1']);

        $method = EcShippingMethod::find($this->shippingMethodId);
        if ($method && $method->in_store_pickup) {
            $this->validate(
                ['branchId' => 'required|integer|min:1'],
                [
                    'branchId.required' => 'Please choose a branch to collect from.',
                    'branchId.min' => 'Please choose a branch to collect from.',
                ]
            );
        }
    }

    public function updatedShippingMethodId(): void
    {
        // Reset the branch when switching to a delivery method so it is not carried over.
        $method = EcShippingMethod::find($this->shippingMethodId);
        if (! $method || ! $method->in_store_pickup) {
            $this->branchId = 0;
        }
    }

    public function placeOrder(): void
    {
        $this->errorMessage = '';

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            $this->errorMessage = 'Please sign in to place an order.';

            return;
        }

        $items = $this->cart->getItems();
        if (empty($items)) {
            $this->errorMessage = 'Your basket is empty.';

            return;
        }

        $shippingMethod = EcShippingMethod::find($this->shippingMethodId);
        if (! $shippingMethod) {
            $this->errorMessage = 'Please select a shipping method.';

            return;
        }

        if ($shippingMethod->in_store_pickup && ! $this->branchId) {
            $this->errorMessage = 'Please choose a branch to collect from.';
            $this->step = 2;

            return;
        }

        $paymentMethod = $this->checkoutPaymentMethodsQuery()
            ->where('id', $this->paymentMethodId)
            ->first();
        if (! $paymentMethod) {
            $this->errorMessage = 'Please select a valid payment method (PayPal or pay in store).';

            return;
        }

        foreach ($items as $item) {
            if (($item['item_type'] ?? 'catalogue') !== 'catalogue') {
                continue;
            }
            $available = (int) ($item['available_stock'] ?? 0);
            if ($available <= 0) {
                $this->errorMessage = 'One or more items are out of stock. Please remove them before checkout.';

                return;
            }
            if ((int) ($item['quantity'] ?? 0) > $available) {
                $this->errorMessage = 'One or more item quantities exceed available stock. Please update your basket.';

                return;
            }
        }

        try {
            DB::beginTransaction();

            // Resolve or create address
            if ($this->newAddress) {
                $address = CustomerAddress::create([
                    'customer_id' => $customer->customer_id,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'company_name' => $this->company_name ?: '-',
                    'street_address' => $this->street_address,
                    'street_address_plus' => $this->street_address_plus ?: '-',
                    'postcode' => $this->postcode,
                    'city' => $this->city,
                    'phone_number' => $this->phone_number,
                    'is_default' => false,
                    'type' => 'shipping',
                    'country_id' => 3,
                ]);
            } else {
                $address = CustomerAddress::find($this->selectedAddressId);
                if (! $address || $address->customer_id !== $customer->customer_id) {
                    throw new \RuntimeException('Invalid delivery address.');
                }
            }

            $productIds = collect($items)
                ->filter(fn ($item) => ($item['item_type'] ?? 'catalogue') === 'catalogue' && ! empty($item['product_id']))
                ->pluck('product_id')
                ->values()
                ->all();
            $products = NgnProduct::whereIn('id', $productIds)->get()->keyBy('id');

            $totalAmount = array_sum(array_column($items, 'line_total'));
            $shippingCost = (float) $shippingMethod->shipping_amount;
            $grandTotal = $totalAmount + $shippingCost;

            // Remove any stale pending order (delete children first — FK on ec_order_items)
            $staleOrderIds = EcOrder::query()
                ->where('customer_id', $customer->id)
                ->where('order_status', 'pending')
                ->where('payment_status', 'pending')
                ->pluck('id');
            if ($staleOrderIds->isNotEmpty()) {
                EcOrderItem::query()->whereIn('order_id', $staleOrderIds)->delete();
                EcOrderShipping::query()->whereIn('order_id', $staleOrderIds)->delete();
                EcOrder::query()->whereIn('id', $staleOrderIds)->delete();
            }

            $isPayPal = str_contains(strtolower((string) $paymentMethod->slug), 'paypal')
                || str_contains(strtolower((string) $paymentMethod->title), 'paypal');

            $order = EcOrder::create([
                'customer_id' => $customer->id,
                'shipping_method_id' => $this->shippingMethodId,
                'payment_method_id' => $paymentMethod->id,
                'customer_address_id' => $address->id,
                'branch_id' => $shippingMethod->in_store_pickup ? ($this->branchId ?: null) : null,
                'order_status' => $isPayPal ? 'pending' : 'Confirmed',
                'shipping_status' => 'pending',
                'payment_status' => 'pending',
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'tax' => 0,
                'discount' => 0,
                'grand_total' => $grandTotal,
                'currency' => 'GBP',
                'order_date' => now(),
            ]);

            foreach ($items as $item) {
                $isSparePart = ($item['item_type'] ?? 'catalogue') === 'sparepart';
                $product = $isSparePart ? null : $products->get($item['product_id']);
                EcOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $isSparePart ? null : ($item['product_id'] ?? null),
                    'item_type' => $item['item_type'] ?? 'catalogue',
                    'product_name' => $product?->name ?? $item['product_name'] ?? ($item['part_number'] ?? 'Spare Part'),
                    'sku' => $product?->sku ?? ($item['sku'] ?? ($item['part_number'] ?? '')),
                    'part_number' => $item['part_number'] ?? null,
                    'sp_part_id' => $item['sp_part_id'] ?? null,
                    'sp_assembly_id' => $item['sp_assembly_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['line_total'],
                    'tax' => 0,
                    'discount' => 0,
                    'line_total' => $item['line_total'],
                    'source_meta' => $item['source_meta'] ?? null,
                ]);
            }

            EcOrderShipping::create([
                'order_id' => $order->id,
                'fulfillment_method' => $shippingMethod->in_store_pickup ? 'pickup' : 'carrier',
                'status' => 'processing',
                'notes' => null,
                'processing_at' => now(),
            ]);

            DB::commit();

            $this->orderId = $order->id;

            if ($isPayPal) {
                // Keep the basket until PayPal confirms payment (cleared on the success return).
                $this->redirectRoute('paypal.directPayment', navigate: false);

                return;
            }

            $this->cart->clear();
            $this->dispatch('cart-updated', count: 0)->to('site.header');
            $this->step = 4;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed', ['error' => $e->getMessage()]);
            $this->errorMessage = 'Something went wrong. Please try again.';
        }
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer
            ? CustomerAddress::where('customer_id', $customer->customer_id)->get()
            : collect();
        $shippingMethods = EcShippingMethod::active()->get();
        $branches = Branch::orderBy('name')->get();
        $paymentMethods = $this->checkoutPaymentMethodsQuery()->get();
        $items = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();
        $shippingMethod = $this->shippingMethodId
            ? EcShippingMethod::find($this->shippingMethodId)
            : null;
        $shippingCost = $shippingMethod ? (float) $shippingMethod->shipping_amount : 0.0;
        $grandTotal = $subtotal + $shippingCost;
        $isSparePartsCheckout = request()->is('spareparts/*');
        $continueShoppingRoute = $isSparePartsCheckout ? 'spareparts.index' : 'shop.home';
        $basketRoute = $isSparePartsCheckout ? 'spareparts.cart' : 'shop.basket';

        return view('livewire.shop.checkout', compact(
            'addresses', 'shippingMethods', 'branches', 'paymentMethods',
            'items', 'subtotal', 'shippingCost', 'grandTotal', 'shippingMethod',
            'isSparePartsCheckout', 'continueShoppingRoute', 'basketRoute'
        ))->layout('components.layouts.public', [
            'title' => $isSparePartsCheckout ? 'Spareparts Checkout | NGN Motors' : 'Checkout | NGN Shop',
            'description' => $isSparePartsCheckout
                ? 'Complete your spareparts order at NGN Motors.'
                : 'Complete your order at NGN Motors.',
        ]);
    }

    /**
     * Payment methods customers may use on this checkout (matches live DB slugs such as paypal, pay-on-store).
     */
    protected function checkoutPaymentMethodsQuery(): Builder
    {
        return EcPaymentMethod::active()
            ->where(function ($query): void {
                $query->whereIn('slug', [
                    'paypal',
                    'pay-on-store',
                    'pay_on_store',
                    'cash',
                    'cash-on-branch',
                    'cash_on_branch',
                ])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%paypal%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%pay on store%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%cash%']);
            })
            ->orderBy('id');
    }
}
