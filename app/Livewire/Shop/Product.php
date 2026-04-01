<?php

namespace App\Livewire\Shop;

use App\Http\Controllers\MailController;
use App\Models\ServiceBooking;
use App\Services\CartService;
use App\Services\ShopService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Product extends Component
{
    public string $slug;

    public int $selectedVariantId = 0;

    public int $quantity = 1;

    public string $cartMessage = '';

    public bool $cartError = false;

    public int $activeImage = 0;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $reg_no = '';

    public string $message = '';

    public bool $privacy = false;

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
        $customerAuth = Auth::guard('customer')->user();
        if ($customerAuth) {
            $this->name = trim((string) ($customerAuth->name ?? ''));
            $this->email = trim((string) ($customerAuth->email ?? ''));
            $this->phone = trim((string) ($customerAuth->phone ?? ''));
        }
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
        $max = $this->getSelectedVariantStock();
        if ($max > 0 && $this->quantity < min(100, $max)) {
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

        if (! $productId) {
            $this->cartMessage = 'Please select a variant first.';
            $this->cartError = true;

            return;
        }

        $stock = $this->getSelectedVariantStock();
        if ($stock <= 0) {
            $this->cartMessage = 'This item is currently out of stock. Please send an enquiry below.';
            $this->cartError = true;

            return;
        }

        if ($this->quantity > $stock) {
            $this->quantity = max(1, $stock);
        }

        $this->cart->add($productId, $this->quantity);
        $this->cartMessage = 'Added to basket!';
        $this->cartError = false;
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function submitEnquiry(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:7'],
            'message' => ['required', 'string', 'min:5'],
            'privacy' => ['accepted'],
        ]);

        $product = $this->shop->getProductBySlug($this->slug);
        if (! $product) {
            abort(404);
        }

        $selectedVariant = collect($product['variants'] ?? [])->firstWhere('id', $this->selectedVariantId);
        $customerAuth = Auth::guard('customer')->user();

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => 'shop',
            'service_type' => 'Shop stock enquiry',
            'subject' => trim(($product['name'] ?? 'Shop product').' stock enquiry'),
            'description' => implode(' | ', array_filter([
                'Source: livewire.shop.product',
                'Product: '.($product['name'] ?? ''),
                'Slug: '.($product['slug'] ?? ''),
                ! empty($selectedVariant['sku']) ? 'Variant SKU: '.$selectedVariant['sku'] : null,
                ! empty($selectedVariant['variation']) ? 'Variant: '.$selectedVariant['variation'] : null,
                'Customer message: '.trim($this->message),
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim($this->name),
            'phone' => trim($this->phone),
            'reg_no' => trim($this->reg_no) ?: 'N/A',
            'email' => trim($this->email) ?: null,
        ]);

        app(MailController::class)->sendBookingConfirmation($booking);

        session()->flash('enquiry_success', 'Enquiry received. Our team will contact you shortly.');
        $this->privacy = false;
    }

    private function getSelectedVariantStock(): int
    {
        if (! $this->selectedVariantId) {
            return 0;
        }

        $availability = $this->shop->getProductAvailability($this->selectedVariantId);

        return max(0, (int) ($availability['total_balance'] ?? 0));
    }

    public function render()
    {
        $product = $this->shop->getProductBySlug($this->slug);

        if (! $product) {
            abort(404);
        }

        if (! $this->selectedVariantId && ! empty($product['variants'])) {
            $this->selectedVariantId = $product['variants'][0]['id'];
        }

        $availability = $this->selectedVariantId
            ? $this->shop->getProductAvailability($this->selectedVariantId)
            : ['total_balance' => 0, 'branches' => []];

        return view('livewire.shop.product', compact('product', 'availability'))
            ->layout('components.layouts.public', [
                'title' => ($product['meta_title'] ?: $product['name']).' | NGN Shop',
                'description' => $product['meta_description'] ?: 'Buy '.$product['name'].' at NGN Motors London.',
            ]);
    }
}
