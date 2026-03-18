<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use App\Services\ShopService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Home extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'sort', history: true)]
    public string $sort = 'newest';

    #[Url(as: 'cat', history: true, except: '')]
    public string $categorySlug = '';

    #[Url(as: 'brand', history: true, except: '')]
    public string $brandSlug = '';

    public int $perPage = 12;

    public string $cartMessage = '';

    protected ShopService $shop;
    protected CartService $cart;

    public function boot(ShopService $shop, CartService $cart): void
    {
        $this->shop = $shop;
        $this->cart = $cart;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedCategorySlug(): void
    {
        $this->resetPage();
    }

    public function updatedBrandSlug(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $this->cart->add($productId, 1);
        $this->cartMessage = 'Added to basket!';
        $this->dispatch('cart-updated', count: $this->cart->count());
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categorySlug = '';
        $this->brandSlug = '';
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = $this->shop->getCategories();
        $brands     = $this->shop->getBrands();

        $categorySlugs = $this->categorySlug ? [$this->categorySlug] : [];
        $brandSlugs    = $this->brandSlug ? [$this->brandSlug] : [];

        $products = $this->shop->getProducts(
            perPage: $this->perPage,
            page: $this->getPage(),
            search: $this->search ?: null,
            sort: $this->sort,
            categorySlugs: $categorySlugs,
            brandSlugs: $brandSlugs,
        );

        $categoryName = $this->categorySlug
            ? $categories->firstWhere('slug', $this->categorySlug)?->name
            : null;

        $brandName = $this->brandSlug
            ? $brands->firstWhere('slug', $this->brandSlug)?->name
            : null;

        return view('livewire.shop.home', compact(
            'products', 'categories', 'brands', 'categoryName', 'brandName'
        ))->layout('components.layouts.public', [
            'title'       => ($categoryName ?? 'Motorcycle Parts & Accessories') . ' | NGN Shop',
            'description' => 'Shop motorcycle accessories, helmets, spare parts & GPS trackers at NGN Motors London.',
        ]);
    }
}
