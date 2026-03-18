<?php

namespace App\Services;

use App\Models\NgnProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Session-based cart for guests; merges into DB pending order on checkout.
 * Cart items stored as: ['product_id' => [...], ...]
 */
class CartService
{
    private const SESSION_KEY = 'ngn_cart';

    /**
     * Get all cart items as a collection of arrays with product data attached.
     */
    public function getItems(): array
    {
        $raw = Session::get(self::SESSION_KEY, []);

        if (empty($raw)) {
            return [];
        }

        $ids = array_keys($raw);
        $products = NgnProduct::whereIn('id', $ids)
            ->where('is_ecommerce', 1)
            ->where('dead', 0)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($raw as $productId => $row) {
            $product = $products->get($productId);
            if (!$product) {
                continue;
            }
            $items[] = [
                'product_id'   => $productId,
                'quantity'     => $row['quantity'],
                'product_name' => $product->name,
                'variation'    => $product->variation,
                'sku'          => $product->sku,
                'image_url'    => $product->image_url,
                'slug'         => $product->slug,
                'unit_price'   => (float) $product->normal_price,
                'line_total'   => (float) $product->normal_price * $row['quantity'],
            ];
        }

        return $items;
    }

    /**
     * Add or increment quantity.
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = ['quantity' => $quantity];
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Set exact quantity for a product.
     */
    public function update(int $productId, int $quantity): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = ['quantity' => min($quantity, 100)];
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Remove a product from cart.
     */
    public function remove(int $productId): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Get total item count (sum of all quantities).
     */
    public function count(): int
    {
        $raw = Session::get(self::SESSION_KEY, []);
        return array_sum(array_column($raw, 'quantity'));
    }

    /**
     * Subtotal before shipping.
     */
    public function subtotal(): float
    {
        return array_sum(array_column($this->getItems(), 'line_total'));
    }

    /**
     * Check if the cart is empty.
     */
    public function isEmpty(): bool
    {
        return empty(Session::get(self::SESSION_KEY, []));
    }

    /**
     * Return the raw session array.
     */
    public function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
