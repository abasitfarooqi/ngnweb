<div>

{{-- Hero strip --}}
<div class="bg-gray-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold">
                    {{ $categoryName ?? ($brandName ?? 'NGN Shop') }}
                </h1>
                <p class="mt-1 text-gray-300 text-sm">
                    Accessories, helmets, spare parts, GPS trackers &amp; more
                </p>
            </div>
            <a href="{{ route('shop.basket') }}"
               class="inline-flex items-center gap-2 bg-brand-red hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 transition">
                <flux:icon name="shopping-bag" class="h-5 w-5" />
                Basket
            </a>
        </div>
    </div>
</div>

{{-- Cart flash notification --}}
@if($cartMessage)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
         x-show="show" x-transition.opacity
         class="fixed top-4 right-4 z-50 flex items-center gap-2 bg-green-600 text-white text-sm px-4 py-3 shadow-xl">
        <flux:icon name="check-circle" class="h-4 w-4 flex-shrink-0" />
        {{ $cartMessage }}
    </div>
@endif

{{-- Quick category nav (desktop scroll, mobile wrap) --}}
<div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex overflow-x-auto gap-0 scrollbar-hide -mb-px">
            <button wire:click="$set('categorySlug', '')"
                    class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap {{ !$categorySlug ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-brand-red hover:border-gray-300' }}">
                All
            </button>
            @foreach($categories as $cat)
                <button wire:click="$set('categorySlug', '{{ $cat->slug }}')"
                        class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap {{ $categorySlug === $cat->slug ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-brand-red hover:border-gray-300' }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Top bar: mobile filter button + sort --}}
    <div class="flex items-center gap-3 mb-5 flex-wrap">

        {{-- Mobile filter toggle --}}
        <div x-data="{ open: false }" class="lg:hidden">
            <button @click="open = !open"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:border-brand-red hover:text-brand-red transition">
                <flux:icon name="adjustments-horizontal" class="h-4 w-4" />
                Filters
                @if($categorySlug || $brandSlug)
                    <span class="w-2 h-2 bg-brand-red"></span>
                @endif
            </button>

            {{-- Mobile filter drawer --}}
            <div x-show="open" x-transition.opacity
                 x-cloak style="display:none;"
                 class="fixed inset-0 z-40 flex">
                <div @click="open = false" class="absolute inset-0 bg-black/50"></div>
                <div class="relative z-50 w-72 max-w-full bg-white dark:bg-gray-900 h-full overflow-y-auto p-5 shadow-xl">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-gray-900 dark:text-white">Filter Products</h3>
                        <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <flux:icon name="x-mark" class="h-5 w-5" />
                        </button>
                    </div>

                    {{-- Mobile Search --}}
                    <div class="mb-5">
                        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search products…" icon="magnifying-glass" clearable />
                    </div>

                    {{-- Mobile Categories --}}
                    <div class="mb-5">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Categories</p>
                        <div class="space-y-1">
                            <button wire:click="$set('categorySlug', '')" @click="open = false"
                                    class="w-full text-left text-sm px-2 py-1.5 {{ !$categorySlug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300' }} hover:text-brand-red transition">
                                All Categories
                            </button>
                            @foreach($categories as $cat)
                                <button wire:click="$set('categorySlug', '{{ $cat->slug }}')" @click="open = false"
                                        class="w-full text-left text-sm px-2 py-1.5 {{ $categorySlug === $cat->slug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300' }} hover:text-brand-red transition">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mobile Brands --}}
                    <div class="mb-5">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Brands</p>
                        <div class="space-y-1">
                            <button wire:click="$set('brandSlug', '')" @click="open = false"
                                    class="w-full text-left text-sm px-2 py-1.5 {{ !$brandSlug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300' }} hover:text-brand-red transition">
                                All Brands
                            </button>
                            @foreach($brands as $brand)
                                <button wire:click="$set('brandSlug', '{{ $brand->slug }}')" @click="open = false"
                                        class="w-full text-left text-sm px-2 py-1.5 {{ $brandSlug === $brand->slug ? 'text-brand-red font-semibold' : 'text-gray-700 dark:text-gray-300' }} hover:text-brand-red transition">
                                    {{ $brand->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($search || $categorySlug || $brandSlug)
                        <button wire:click="clearFilters" @click="open = false"
                                class="w-full text-sm text-brand-red font-medium hover:underline mt-2">
                            Clear all filters
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Results count --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 flex-1">
            <span wire:loading.remove>{{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}</span>
            <span wire:loading class="italic">Loading…</span>
        </p>

        {{-- Sort (desktop + mobile) --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 hidden sm:inline">Sort:</span>
            <flux:select wire:model.live="sort" class="text-sm">
                <flux:select.option value="newest">Newest</flux:select.option>
                <flux:select.option value="price_low">Price: Low → High</flux:select.option>
                <flux:select.option value="price_high">Price: High → Low</flux:select.option>
                <flux:select.option value="name">Name A–Z</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Active filter chips --}}
    @if($categoryName || $brandName || $search)
        <div class="flex flex-wrap gap-2 mb-5">
            @if($categoryName)
                <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                    {{ $categoryName }}
                    <button wire:click="$set('categorySlug', '')" class="ml-1 text-gray-400 hover:text-brand-red">&times;</button>
                </span>
            @endif
            @if($brandName)
                <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                    {{ $brandName }}
                    <button wire:click="$set('brandSlug', '')" class="ml-1 text-gray-400 hover:text-brand-red">&times;</button>
                </span>
            @endif
            @if($search)
                <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-sm px-3 py-1 text-gray-700 dark:text-gray-300">
                    "{{ $search }}"
                    <button wire:click="$set('search', '')" class="ml-1 text-gray-400 hover:text-brand-red">&times;</button>
                </span>
            @endif
        </div>
    @endif

    <div class="flex gap-8">

        {{-- Desktop sidebar --}}
        <aside class="hidden lg:block w-64 flex-shrink-0" x-data="{ openCat: true, openBrand: true }">

            {{-- Search --}}
            <div class="mb-5">
                <flux:input wire:model.live.debounce.400ms="search" placeholder="Search…" icon="magnifying-glass" clearable />
            </div>

            {{-- Categories --}}
            <div class="mb-6">
                <button @click="openCat = !openCat"
                        class="w-full flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">
                    <span>Categories</span>
                    <span x-show="openCat"><flux:icon name="chevron-up" class="h-3.5 w-3.5" /></span>
                    <span x-show="!openCat" x-cloak style="display:none;"><flux:icon name="chevron-down" class="h-3.5 w-3.5" /></span>
                </button>
                <div x-show="openCat" x-transition class="space-y-0.5">
                    <button wire:click="$set('categorySlug', '')"
                            class="w-full text-left text-sm px-2 py-1.5 {{ !$categorySlug ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-400 hover:text-brand-red' }} transition">
                        All Categories
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="$set('categorySlug', '{{ $cat->slug }}')"
                                class="w-full text-left text-sm px-2 py-1.5 {{ $categorySlug === $cat->slug ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-400 hover:text-brand-red' }} transition">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Brands --}}
            <div class="mb-6">
                <button @click="openBrand = !openBrand"
                        class="w-full flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">
                    <span>Brands</span>
                    <span x-show="openBrand"><flux:icon name="chevron-up" class="h-3.5 w-3.5" /></span>
                    <span x-show="!openBrand" x-cloak style="display:none;"><flux:icon name="chevron-down" class="h-3.5 w-3.5" /></span>
                </button>
                <div x-show="openBrand" x-transition class="space-y-0.5">
                    <button wire:click="$set('brandSlug', '')"
                            class="w-full text-left text-sm px-2 py-1.5 {{ !$brandSlug ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-400 hover:text-brand-red' }} transition">
                        All Brands
                    </button>
                    @foreach($brands as $brand)
                        <button wire:click="$set('brandSlug', '{{ $brand->slug }}')"
                                class="w-full text-left text-sm px-2 py-1.5 {{ $brandSlug === $brand->slug ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-400 hover:text-brand-red' }} transition">
                            {{ $brand->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if($search || $categorySlug || $brandSlug)
                <button wire:click="clearFilters"
                        class="w-full text-xs text-brand-red font-medium hover:underline text-left">
                    ✕ Clear all filters
                </button>
            @endif
        </aside>

        {{-- Product grid --}}
        <div class="flex-1 min-w-0">
            @if($products->isEmpty())
                <div class="text-center py-24">
                    <flux:icon name="magnifying-glass" class="h-14 w-14 text-gray-200 dark:text-gray-700 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No products found.</p>
                    <button wire:click="clearFilters" class="mt-4 text-brand-red text-sm hover:underline">Clear filters</button>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4" wire:loading.class="opacity-60 pointer-events-none">
                    @foreach($products as $product)
                        <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-brand-red hover:shadow-md transition-all flex flex-col">
                            <a href="{{ route('shop.product', $product->slug) }}" class="block overflow-hidden bg-gray-50 dark:bg-gray-700">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-36 sm:h-44 object-contain p-3 group-hover:scale-105 transition duration-300"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-36 sm:h-44 flex items-center justify-center">
                                        <flux:icon name="photo" class="h-12 w-12 text-gray-300 dark:text-gray-600" />
                                    </div>
                                @endif
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-1">{{ $product->brand }}</p>
                                <a href="{{ route('shop.product', $product->slug) }}"
                                   class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red transition line-clamp-2 flex-1 leading-snug">
                                    {{ $product->name }}
                                </a>
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                        £{{ number_format($product->normal_price, 2) }}
                                    </span>
                                    @if($product->global_stock > 0)
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ In stock</span>
                                    @else
                                        <span class="text-xs text-red-500 font-medium">Out of stock</span>
                                    @endif
                                </div>
                                @if($product->global_stock > 0)
                                    <a href="{{ route('shop.product', $product->slug) }}"
                                       class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs font-semibold hover:bg-brand-red transition">
                                        View Product
                                    </a>
                                @else
                                    <a href="{{ route('shop.product', $product->slug) }}#stock-enquiry"
                                       class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 border border-brand-red text-brand-red text-xs font-semibold hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        Enquire Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>

</div>

</div>
