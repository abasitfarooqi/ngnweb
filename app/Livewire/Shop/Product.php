<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use App\Services\ShopService;
use Livewire\Component;

class Product extends Component
{
    public string $slug;

    public int $selectedVariantId = 0;
    public int $quantity = 1;
    public string $cartMessage = '';
    public bool $cartError = false;
    public int $activeImage = 0;

    protected ShopService $shop;
    protected CartService $cart;

    public function boot(ShopService $shop, CartService $cart): void
    {
        $this->shop = $shop;
        $this->cart = $cart;
    }

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function selectVariant(int $id): void
    {
        $this->selectedVariantId = $id;
        $this->cartMessage = '';
    }

    public function setImage(int $index): void
    {
        $this->activeImage = $index;
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < 100) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $productId = $this->selectedVariantId ?: 0;

        if (!$productId) {
            $this->cartMessage = 'Please select a variant first.';
            $this->cartError = true;

            return;
        }

        $this->cart->add($productId, $this->quantity);
        $this->cartMessage = 'Added to basket!';
        $this->cartError = false;
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function render()
    {
        $product = $this->shop->getProductBySlug($this->slug);

        if (!$product) {
            abort(404);
        }

        if (!$this->selectedVariantId && !empty($product['variants'])) {
            $this->selectedVariantId = $product['variants'][0]['id'];
        }

        $availability = $this->selectedVariantId
            ? $this->shop->getProductAvailability($this->selectedVariantId)
            : ['total_balance' => 0, 'branches' => []];

        return view('livewire.shop.product', compact('product', 'availability'))
            ->layout('components.layouts.public', [
                'title'       => ($product['meta_title'] ?: $product['name']) . ' | NGN Shop',
                'description' => $product['meta_description'] ?: 'Buy ' . $product['name'] . ' at NGN Motors London.',
            ]);
    }
}
