<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use Livewire\Component;

class Basket extends Component
{
    public string $couponCode = '';

    public string $couponMessage = '';

    public ?int $pendingRemoveIndex = null;

    public bool $pendingClearAll = false;

    protected CartService $cart;

    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function incrementQuantityAt(int $index): void
    {
        $items = array_values($this->cart->getItems());
        if (! isset($items[$index])) {
            return;
        }
        $item = $items[$index];
        $rowId = $item['row_id'];
        $newQty = (int) $item['quantity'] + 1;
        if (($item['item_type'] ?? 'catalogue') === 'sparepart') {
            $newQty = min($newQty, 100);
        } else {
            $avail = (int) ($item['available_stock'] ?? 0);
            $newQty = min($newQty, max(0, $avail), 100);
        }
        $this->cart->update($rowId, $newQty);
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function decrementQuantityAt(int $index): void
    {
        $items = array_values($this->cart->getItems());
        if (! isset($items[$index])) {
            return;
        }
        $item = $items[$index];
        $rowId = $item['row_id'];
        $newQty = (int) $item['quantity'] - 1;
        if ($newQty < 1) {
            $this->cart->remove($rowId);
        } else {
            $this->cart->update($rowId, $newQty);
        }
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function requestRemove(int $index): void
    {
        $this->pendingRemoveIndex = $index;
        $this->pendingClearAll = false;
    }

    public function requestClearBasket(): void
    {
        $this->pendingClearAll = true;
        $this->pendingRemoveIndex = null;
    }

    public function cancelPendingRemoval(): void
    {
        $this->pendingRemoveIndex = null;
        $this->pendingClearAll = false;
    }

    public function confirmPendingRemoval(): void
    {
        if ($this->pendingClearAll) {
            $this->clear();

            return;
        }
        if ($this->pendingRemoveIndex === null) {
            return;
        }
        $items = array_values($this->cart->getItems());
        $idx = $this->pendingRemoveIndex;
        $this->cancelPendingRemoval();
        if (! isset($items[$idx])) {
            return;
        }
        $this->cart->remove($items[$idx]['row_id']);
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function clear(): void
    {
        $this->cart->clear();
        $this->cancelPendingRemoval();
        $this->dispatch('cart-updated', count: 0)->to('site.header');
    }

    public function render()
    {
        $items = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();
        $isEmpty = empty($items);

        return view('livewire.shop.basket', compact('items', 'subtotal', 'isEmpty'))
            ->layout('components.layouts.public', [
                'title' => 'Your Basket | NGN Shop',
                'description' => 'Review your basket before checkout.',
            ]);
    }
}
