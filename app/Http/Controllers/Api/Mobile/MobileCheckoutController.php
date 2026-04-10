<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CustomerAuth;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcOrderItem;
use App\Models\Ecommerce\EcPaymentMethod;
use App\Models\Ecommerce\EcShippingMethod;
use App\Models\NgnProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileCheckoutController extends Controller
{
    public function cart(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = $this->activeCartOrder($customer);

        return response()->json([
            'data' => $order ? $this->mapOrder($order) : [
                'id' => null,
                'currency' => 'GBP',
                'items' => [],
                'item_count' => 0,
                'subtotal' => 0.0,
                'shipping' => 0.0,
                'discount' => 0.0,
                'tax' => 0.0,
                'grand_total' => 0.0,
            ],
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'product_id' => ['required', 'integer', 'exists:ngn_products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $product = NgnProduct::query()
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            })
            ->findOrFail((int) $payload['product_id']);

        $order = $this->getOrCreateCartOrder($customer);
        $quantityToAdd = (int) ($payload['quantity'] ?? 1);

        $item = EcOrderItem::query()
            ->where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->where('item_type', 'catalogue')
            ->first();

        if (! $item) {
            $item = new EcOrderItem([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'item_type' => 'catalogue',
            ]);
        }

        $item->quantity = min(100, (int) ($item->quantity ?? 0) + $quantityToAdd);
        $this->fillCatalogItemPricing($item, $product);
        $item->save();

        $this->recalculateOrder($order->fresh());

        return response()->json([
            'message' => 'Item added to cart.',
            'data' => $this->mapOrder($order->fresh('items.product', 'shippingMethod', 'paymentMethod')),
        ], 201);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $order = $this->activeCartOrder($customer);
        if (! $order) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $item = EcOrderItem::query()
            ->where('order_id', $order->id)
            ->findOrFail($id);

        if ((int) $payload['quantity'] === 0) {
            $item->delete();
        } else {
            $item->quantity = (int) $payload['quantity'];
            if ($item->item_type === 'catalogue' && $item->product) {
                $this->fillCatalogItemPricing($item, $item->product);
            } else {
                $unit = (float) ($item->unit_price ?? 0);
                $lineTotal = round($unit * $item->quantity, 2);
                $item->total_price = $lineTotal;
                $item->line_total = $lineTotal;
            }
            $item->save();
        }

        $this->recalculateOrder($order->fresh());

        return response()->json([
            'message' => 'Cart item updated.',
            'data' => $this->mapOrder($order->fresh('items.product', 'shippingMethod', 'paymentMethod')),
        ]);
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = $this->activeCartOrder($customer);
        if (! $order) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        EcOrderItem::query()
            ->where('order_id', $order->id)
            ->where('id', $id)
            ->delete();

        $this->recalculateOrder($order->fresh());

        return response()->json([
            'message' => 'Cart item removed.',
            'data' => $this->mapOrder($order->fresh('items.product', 'shippingMethod', 'paymentMethod')),
        ]);
    }

    public function quote(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'shipping_method_id' => ['nullable', 'integer', 'exists:ec_shipping_methods,id'],
            'payment_method_id' => ['nullable', 'integer', 'exists:ec_payment_methods,id'],
        ]);

        $order = $this->activeCartOrder($customer);
        if (! $order) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        if (! empty($payload['shipping_method_id'])) {
            $order->shipping_method_id = (int) $payload['shipping_method_id'];
        }
        if (! empty($payload['payment_method_id'])) {
            $order->payment_method_id = (int) $payload['payment_method_id'];
        }
        $order->save();

        $this->recalculateOrder($order->fresh());

        return response()->json([
            'data' => $this->mapOrder($order->fresh('items.product', 'shippingMethod', 'paymentMethod')),
            'available_shipping_methods' => EcShippingMethod::query()->active()->get(['id', 'name', 'slug', 'shipping_amount', 'in_store_pickup']),
            'available_payment_methods' => EcPaymentMethod::query()->active()->get(['id', 'title', 'slug']),
        ]);
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'shipping_method_id' => ['required', 'integer', 'exists:ec_shipping_methods,id'],
            'payment_method_id' => ['required', 'integer', 'exists:ec_payment_methods,id'],
            'customer_address_id' => ['nullable', 'integer', 'exists:customer_addresses,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $order = $this->activeCartOrder($customer);
        if (! $order || $order->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $order->shipping_method_id = (int) $payload['shipping_method_id'];
        $order->payment_method_id = (int) $payload['payment_method_id'];
        $order->customer_address_id = $payload['customer_address_id'] ?? null;
        $order->branch_id = $payload['branch_id'] ?? null;
        $order->order_status = 'Pending';
        $order->payment_status = 'Pending';
        $order->shipping_status = 'Pending';
        $order->payment_reference = ! empty($payload['notes']) ? substr((string) $payload['notes'], 0, 255) : $order->payment_reference;
        $order->save();

        $this->recalculateOrder($order->fresh());

        return response()->json([
            'message' => 'Order placed successfully.',
            'data' => [
                'order_id' => $order->id,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'grand_total' => (float) $order->grand_total,
            ],
        ], 201);
    }

    private function customer(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum');

        return $actor instanceof CustomerAuth ? $actor : null;
    }

    private function activeCartOrder(CustomerAuth $customer): ?EcOrder
    {
        return EcOrder::query()
            ->with(['items.product', 'shippingMethod', 'paymentMethod'])
            ->where('customer_id', $customer->id)
            ->whereIn('order_status', ['Draft', 'Pending'])
            ->latest('id')
            ->first();
    }

    private function getOrCreateCartOrder(CustomerAuth $customer): EcOrder
    {
        $existing = $this->activeCartOrder($customer);
        if ($existing) {
            return $existing;
        }

        return EcOrder::query()->create([
            'order_date' => now(),
            'order_status' => 'Draft',
            'payment_status' => 'Draft',
            'shipping_status' => 'Draft',
            'currency' => 'GBP',
            'customer_id' => $customer->id,
            'total_amount' => 0,
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'grand_total' => 0,
        ]);
    }

    private function fillCatalogItemPricing(EcOrderItem $item, NgnProduct $product): void
    {
        $unit = round((float) ($product->normal_price ?? 0), 2);
        $lineTotal = round($unit * (int) $item->quantity, 2);

        $item->product_name = $product->name;
        $item->sku = $product->sku;
        $item->unit_price = $unit;
        $item->total_price = $lineTotal;
        $item->line_total = $lineTotal;
        $item->discount = 0;
        $item->tax = 0;
    }

    private function recalculateOrder(EcOrder $order): void
    {
        $order->loadMissing('items');
        $subtotal = round((float) $order->items->sum(fn (EcOrderItem $item) => (float) ($item->line_total ?? 0)), 2);
        $shipping = (float) ($order->shippingMethod?->shipping_amount ?? 0);
        $discount = round((float) ($order->discount ?? 0), 2);
        $tax = round((float) ($order->tax ?? 0), 2);
        $grand = round($subtotal + $shipping + $tax - $discount, 2);

        $order->total_amount = $subtotal;
        $order->shipping_cost = $shipping;
        $order->discount = $discount;
        $order->tax = $tax;
        $order->grand_total = $grand;
        $order->save();
    }

    private function mapOrder(EcOrder $order): array
    {
        $order->loadMissing(['items.product', 'shippingMethod', 'paymentMethod']);

        return [
            'id' => $order->id,
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'shipping_status' => $order->shipping_status,
            'currency' => $order->currency ?: 'GBP',
            'shipping_method' => $order->shippingMethod ? [
                'id' => $order->shippingMethod->id,
                'name' => $order->shippingMethod->name,
                'slug' => $order->shippingMethod->slug,
            ] : null,
            'payment_method' => $order->paymentMethod ? [
                'id' => $order->paymentMethod->id,
                'title' => $order->paymentMethod->title,
                'slug' => $order->paymentMethod->slug,
            ] : null,
            'items' => $order->items->map(fn (EcOrderItem $item) => [
                'id' => $item->id,
                'item_type' => $item->item_type,
                'product_id' => $item->product_id,
                'name' => $item->product_name ?: $item->product?->name,
                'sku' => $item->sku ?: $item->product?->sku,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) ($item->unit_price ?? 0),
                'line_total' => (float) ($item->line_total ?? 0),
                'image_url' => $item->product?->image_url,
            ])->values(),
            'item_count' => (int) $order->items->sum('quantity'),
            'subtotal' => (float) ($order->total_amount ?? 0),
            'shipping' => (float) ($order->shipping_cost ?? 0),
            'discount' => (float) ($order->discount ?? 0),
            'tax' => (float) ($order->tax ?? 0),
            'grand_total' => (float) ($order->grand_total ?? 0),
        ];
    }
}
