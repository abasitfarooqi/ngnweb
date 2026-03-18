<div>

{{-- Hero strip --}}
<div class="bg-gray-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold">
                    {{ $categoryName ?? ($brandName ?? 'NGN Shop') }}
                </h1>
                <p class="mt-1 text-gray-300 text-sm">
                    Accessories, helmets, spare parts, GPS trackers &amp; more
                </p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('shop.basket') }}"
                   class="inline-flex items-center gap-2 bg-brand-red hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 transition">
                    <flux:icon name="shopping-bag" class="h-5 w-5" />
                    Basket
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Add to cart flash --}}
@if($cartMessage)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed top-4 right-4 z-50 bg-green-600 text-white text-sm px-4 py-3 shadow-lg">
        <flux:icon name="check-circle" class="inline h-4 w-4 mr-1" /> {{ $cartMessage }}
    </div>
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar filters --}}
        <aside class="w-full lg:w-56 flex-shrink-0">
            <div x-data="{ openCat: true, openBrand: true }">

                {{-- Search --}}
                <div class="mb-4">
                    <flux:input wire:model.live.debounce.400ms="search"
                        placeholder="Search products…"
                        icon="magnifying-glass"
                        clearable />
                </div>

                {{-- Sort --}}
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Sort By</p>
                    <flux:select wire:model.live="sort">
                        <flux:select.option value="newest">Newest First</flux:select.option>
                        <flux:select.option value="price_low">Price: Low to High</flux:select.option>
                        <flux:select.option value="price_high">Price: High to Low</flux:select.option>
                        <flux:select.option value="name">Name A–Z</flux:select.option>
                    </flux:select>
                </div>

                {{-- Categories --}}
                <div class="mb-6">
                    <button @click="openCat = !openCat"
                            class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        <span>Categories</span>
                        <span class="inline-block" x-show="openCat"><flux:icon name="chevron-up" class="h-4 w-4" /></span>
                        <span class="inline-block" x-show="!openCat" x-cloak style="display: none;"><flux:icon name="chevron-down" class="h-4 w-4" /></span>
                    </button>
                    <div x-show="openCat" x-transition class="space-y-1">
                        <button wire:click="$set('categorySlug', '')"
                                class="w-full text-left text-sm px-2 py-1 {{ !$categorySlug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">
                            All Categories
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="$set('categorySlug', '{{ $cat->slug }}')"
                                    class="w-full text-left text-sm px-2 py-1 {{ $categorySlug === $cat->slug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Brands --}}
                <div class="mb-6">
                    <button @click="openBrand = !openBrand"
                            class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        <span>Brands</span>
                        <span class="inline-block" x-show="openBrand"><flux:icon name="chevron-up" class="h-4 w-4" /></span>
                        <span class="inline-block" x-show="!openBrand" x-cloak style="display: none;"><flux:icon name="chevron-down" class="h-4 w-4" /></span>
                    </button>
                    <div x-show="openBrand" x-transition class="space-y-1">
                        <button wire:click="$set('brandSlug', '')"
                                class="w-full text-left text-sm px-2 py-1 {{ !$brandSlug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">
                            All Brands
                        </button>
                        @foreach($brands as $brand)
                            <button wire:click="$set('brandSlug', '{{ $brand->slug }}')"
                                    class="w-full text-left text-sm px-2 py-1 {{ $brandSlug === $brand->slug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-brand-red' }} transition">
                                {{ $brand->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($search || $categorySlug || $brandSlug)
                    <button wire:click="clearFilters"
                            class="w-full text-xs text-brand-red hover:underline text-left mt-2">
                        Clear all filters
                    </button>
                @endif
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">

            {{-- Active filter chips --}}
            @if($categoryName || $brandName || $search)
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($categoryName)
                        <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                            {{ $categoryName }}
                            <button wire:click="$set('categorySlug', '')" class="hover:text-brand-red ml-1">&times;</button>
                        </span>
                    @endif
                    @if($brandName)
                        <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                            {{ $brandName }}
                            <button wire:click="$set('brandSlug', '')" class="hover:text-brand-red ml-1">&times;</button>
                        </span>
                    @endif
                    @if($search)
                        <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                            "{{ $search }}"
                            <button wire:click="$set('search', '')" class="hover:text-brand-red ml-1">&times;</button>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Results count --}}
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }} found
            </p>

            {{-- Loading overlay --}}
            <div wire:loading.delay class="text-sm text-gray-500 py-4 text-center">Loading…</div>

            {{-- Product grid --}}
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <flux:icon name="magnifying-glass" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">No products found.</p>
                    <button wire:click="clearFilters" class="mt-3 text-brand-red text-sm hover:underline">Clear filters</button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" wire:loading.class="opacity-50">
                    @foreach($products as $product)
                        <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-brand-red transition flex flex-col">
                            <a href="{{ route('shop.product', $product->slug) }}" class="block overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-48 object-contain p-4 group-hover:scale-105 transition duration-300"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <flux:icon name="photo" class="h-10 w-10 text-gray-300" />
                                    </div>
                                @endif
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">{{ $product->brand }}</p>
                                <a href="{{ route('shop.product', $product->slug) }}"
                                   class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red line-clamp-2 mb-2 flex-1">
                                    {{ $product->name }}
                                </a>
                                <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        £{{ number_format($product->normal_price, 2) }}
                                    </span>
                                    <span class="text-xs {{ $product->global_stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $product->global_stock > 0 ? 'In stock' : 'Out of stock' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

</div>
