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

    /** Filter by category primary key (works even when slug is blank in the database). */
    #[Url(as: 'cid', history: true)]
    public ?int $categoryId = null;

    /** Filter by brand primary key (works even when slug is blank in the database). */
    #[Url(as: 'bid', history: true)]
    public ?int $brandId = null;

    /** Legacy / shareable filter when slugs exist. */
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

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedBrandId(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $availability = $this->shop->getProductAvailability($productId);
        if (($availability['total_balance'] ?? 0) <= 0) {
            $this->cartMessage = 'This item is currently out of stock. Please send an enquiry.';

            return;
        }

        $this->cart->add($productId, 1);
        $this->cartMessage = 'Added to basket!';
        $this->dispatch('cart-updated', count: $this->cart->count())->to('site.header');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryId = null;
        $this->brandId = null;
        $this->categorySlug = '';
        $this->brandSlug = '';
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function selectCategoryById(?int $id = null): void
    {
        $this->categoryId = $id;
        $this->categorySlug = '';
        $this->resetPage();
    }

    public function selectBrandById(?int $id = null): void
    {
        $this->brandId = $id;
        $this->brandSlug = '';
        $this->resetPage();
    }

    /** @deprecated Use selectCategoryById; kept for ?cat= slug bookmarks. */
    public function selectCategory(?string $slug = null): void
    {
        $this->categoryId = null;
        $this->categorySlug = filled($slug) ? trim($slug) : '';
        $this->resetPage();
    }

    /** @deprecated Use selectBrandById; kept for ?brand= slug bookmarks. */
    public function selectBrand(?string $slug = null): void
    {
        $this->brandId = null;
        $this->brandSlug = filled($slug) ? trim($slug) : '';
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $categories = $this->shop->getCategories()->values();
        $brands = $this->shop->getBrands()->values();

        $categoryIds = [];
        $categorySlugs = [];
        if ($this->categoryId !== null) {
            if ($categories->contains(fn ($c) => (int) $c->id === (int) $this->categoryId)) {
                $categoryIds = [(int) $this->categoryId];
            } else {
                $categoryIds = [-1];
            }
        } elseif (filled($this->categorySlug)) {
            $categorySlugs = [$this->categorySlug];
        }

        $brandIds = [];
        $brandSlugs = [];
        if ($this->brandId !== null) {
            if ($brands->contains(fn ($b) => (int) $b->id === (int) $this->brandId)) {
                $brandIds = [(int) $this->brandId];
            } else {
                $brandIds = [-1];
            }
        } elseif (filled($this->brandSlug)) {
            $brandSlugs = [$this->brandSlug];
        }

        $products = $this->shop->getProducts(
            perPage: $this->perPage,
            page: $this->getPage(),
            search: $this->search ?: null,
            sort: $this->sort,
            categoryIds: $categoryIds,
            brandIds: $brandIds,
            categorySlugs: $categorySlugs,
            brandSlugs: $brandSlugs,
        );

        $categoryName = null;
        if ($this->categoryId !== null) {
            $categoryName = $categories->firstWhere('id', $this->categoryId)?->name;
        } elseif (filled($this->categorySlug)) {
            $needle = strtolower(trim($this->categorySlug));
            $categoryName = $categories->first(
                fn ($c) => strtolower(trim((string) ($c->slug ?? ''))) === $needle
            )?->name;
        }

        $brandName = null;
        if ($this->brandId !== null) {
            $brandName = $brands->firstWhere('id', $this->brandId)?->name;
        } elseif (filled($this->brandSlug)) {
            $needle = strtolower(trim($this->brandSlug));
            $brandName = $brands->first(
                fn ($b) => strtolower(trim((string) ($b->slug ?? ''))) === $needle
            )?->name;
        }

        $categorySlugNorm = strtolower(trim($this->categorySlug));
        $brandSlugNorm = strtolower(trim($this->brandSlug));
        $activeCategoryId = $this->categoryId;
        $activeBrandId = $this->brandId;

        $pageTitle = ($categoryName ?? $brandName ?? 'Motorcycle Parts & Accessories').' | NGN Shop';

        return view('livewire.shop.home', compact(
            'products',
            'categories',
            'brands',
            'categoryName',
            'brandName',
            'categorySlugNorm',
            'brandSlugNorm',
            'activeCategoryId',
            'activeBrandId',
        ))->layout('components.layouts.public', [
            'title' => $pageTitle,
            'description' => 'Shop motorcycle accessories, helmets, spare parts & GPS trackers at NGN Motors London.',
        ]);
    }
}
