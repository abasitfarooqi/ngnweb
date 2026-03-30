<?php

namespace App\Services;

use App\Models\NgnProduct;
use App\Models\SpAssembly;
use App\Models\SpPart;
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

        $normalised = $this->normaliseRawCart($raw);
        $productIds = [];
        $spPartIds = [];
        $spAssemblyIds = [];

        foreach ($normalised as $row) {
            if (($row['type'] ?? 'catalogue') === 'sparepart') {
                if (! empty($row['sp_part_id'])) {
                    $spPartIds[] = (int) $row['sp_part_id'];
                }
                if (! empty($row['sp_assembly_id'])) {
                    $spAssemblyIds[] = (int) $row['sp_assembly_id'];
                }
            } elseif (! empty($row['product_id'])) {
                $productIds[] = (int) $row['product_id'];
            }
        }

        $products = NgnProduct::whereIn('id', $productIds)
            ->where('is_ecommerce', 1)
            ->where('dead', 0)
            ->get()
            ->keyBy('id');

        $spParts = SpPart::query()
            ->whereIn('id', array_values(array_unique($spPartIds)))
            ->get()
            ->keyBy('id');

        $spAssemblies = SpAssembly::query()
            ->whereIn('id', array_values(array_unique($spAssemblyIds)))
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($normalised as $rowId => $row) {
            $type = (string) ($row['type'] ?? 'catalogue');
            $quantity = (int) ($row['quantity'] ?? 1);
            if ($quantity < 1) {
                continue;
            }

            if ($type === 'sparepart') {
                $spPartId = (int) ($row['sp_part_id'] ?? 0);
                $part = $spParts->get($spPartId);
                if (! $part) {
                    continue;
                }

                $assembly = $spAssemblies->get((int) ($row['sp_assembly_id'] ?? 0));
                $fitment = is_array($row['fitment'] ?? null) ? $row['fitment'] : [];
                $partNumber = (string) ($row['part_number'] ?? $part->part_number);
                $lineUnitPrice = (float) ($row['unit_price'] ?? $part->price_gbp_inc_vat ?? 0);

                $items[] = [
                    'row_id' => $rowId,
                    'item_type' => 'sparepart',
                    'product_id' => null,
                    'sp_part_id' => $spPartId,
                    'sp_assembly_id' => $assembly?->id,
                    'part_number' => $partNumber,
                    'quantity' => $quantity,
                    'product_name' => $part->name,
                    'variation' => $assembly?->name ?: 'Spare part',
                    'sku' => $partNumber,
                    'image_url' => $assembly?->image_url ?: null,
                    'slug' => null,
                    'unit_price' => $lineUnitPrice,
                    'line_total' => $lineUnitPrice * $quantity,
                    'fitment' => $fitment,
                    'source_meta' => $fitment,
                    'sparepart_url' => '/spareparts/part/'.urlencode($partNumber),
                ];

                continue;
            }

            $productId = (int) ($row['product_id'] ?? 0);
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $unitPrice = (float) $product->normal_price;
            $items[] = [
                'row_id' => $rowId,
                'item_type' => 'catalogue',
                'product_id' => $productId,
                'sp_part_id' => null,
                'sp_assembly_id' => null,
                'part_number' => null,
                'quantity' => $quantity,
                'product_name' => $product->name,
                'variation' => $product->variation,
                'sku' => $product->sku,
                'image_url' => $product->image_url,
                'slug' => $product->slug,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
                'fitment' => null,
                'source_meta' => null,
                'sparepart_url' => null,
            ];
        }

        return $items;
    }

    /**
     * Add or increment quantity.
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $this->addProduct($productId, $quantity);
    }

    public function addProduct(int $productId, int $quantity = 1): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        $rowId = $this->catalogueRowId($productId);
        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] += $quantity;
        } else {
            $cart[$rowId] = [
                'type' => 'catalogue',
                'product_id' => $productId,
                'quantity' => max(1, $quantity),
            ];
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public function addSparePart(
        int $spPartId,
        int $spAssemblyId,
        string $partNumber,
        float $unitPrice,
        int $quantity = 1,
        array $fitment = []
    ): void {
        $cart = Session::get(self::SESSION_KEY, []);
        $rowId = $this->sparepartRowId($spPartId, $spAssemblyId);

        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] += max(1, $quantity);
        } else {
            $cart[$rowId] = [
                'type' => 'sparepart',
                'sp_part_id' => $spPartId,
                'sp_assembly_id' => $spAssemblyId,
                'part_number' => strtoupper(str_replace(' ', '', trim($partNumber))),
                'unit_price' => $unitPrice,
                'quantity' => max(1, $quantity),
                'fitment' => $fitment,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Set exact quantity for a product.
     */
    public function update(string $rowId, int $quantity): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        if ($quantity <= 0) {
            unset($cart[$rowId]);
        } else {
            if (isset($cart[$rowId])) {
                $cart[$rowId]['quantity'] = min($quantity, 100);
            }
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Remove a product from cart.
     */
    public function remove(string $rowId): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        unset($cart[$rowId]);
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

    private function catalogueRowId(int $productId): string
    {
        return 'p:'.$productId;
    }

    private function sparepartRowId(int $spPartId, int $spAssemblyId): string
    {
        return 's:'.$spPartId.':'.$spAssemblyId;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, array<string, mixed>>
     */
    private function normaliseRawCart(array $raw): array
    {
        $normalised = [];

        foreach ($raw as $key => $row) {
            if (! is_array($row)) {
                continue;
            }

            // Backward compatibility for old cart format: [productId => ['quantity' => n]]
            if (! isset($row['type'])) {
                $productId = (int) $key;
                $rowId = $this->catalogueRowId($productId);
                $normalised[$rowId] = [
                    'type' => 'catalogue',
                    'product_id' => $productId,
                    'quantity' => (int) ($row['quantity'] ?? 1),
                ];
                continue;
            }

            $normalised[(string) $key] = $row;
        }

        return $normalised;
    }
}
