<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use Livewire\Component;

class Basket extends Component
{
    public string $couponCode = '';
    public string $couponMessage = '';

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $this->cart->update($productId, $quantity);
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function remove(int $productId): void
    {
        $this->cart->remove($productId);
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function clear(): void
    {
        $this->cart->clear();
        $this->dispatch('cart-updated', count: 0);
    }

    public function render()
    {
        $items    = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();
        $isEmpty  = empty($items);

        return view('livewire.shop.basket', compact('items', 'subtotal', 'isEmpty'))
            ->layout('components.layouts.public', [
                'title'       => 'Your Basket | NGN Shop',
                'description' => 'Review your basket before checkout.',
            ]);
    }
}
