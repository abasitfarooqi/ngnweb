<div class="shop-home-root">

    @php
        $categoryAllActive = $activeCategoryId === null && $categorySlugNorm === '';
        $brandAllActive = $activeBrandId === null && $brandSlugNorm === '';
        $categoryRowActive = fn ($cat): bool => ($activeCategoryId !== null && (int) $cat->id === (int) $activeCategoryId)
            || ($activeCategoryId === null && $categorySlugNorm !== '' && $categorySlugNorm === strtolower(trim((string) ($cat->slug ?? ''))));
        $brandRowActive = fn ($brand): bool => ($activeBrandId !== null && (int) $brand->id === (int) $activeBrandId)
            || ($activeBrandId === null && $brandSlugNorm !== '' && $brandSlugNorm === strtolower(trim((string) ($brand->slug ?? ''))));
    @endphp

    <div wire:loading.delay.shortest class="fixed top-0 left-0 right-0 z-[100] h-0.5 bg-brand-red" aria-hidden="true"></div>

    {{-- Hero --}}
    <div class="bg-gray-900 text-white py-8 border-b border-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1">E-commerce</p>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight">
                        {{ $categoryName ?? ($brandName ?? 'NGN Shop') }}
                    </h1>
                    <p class="mt-2 text-gray-400 text-sm max-w-xl">
                        Accessories, helmets, spare parts, GPS trackers and more — filtered live as you browse.
                    </p>
                </div>
                <a href="{{ route('shop.basket') }}"
                   class="inline-flex items-center gap-2 bg-brand-red hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 border border-red-600 transition">
                    <flux:icon name="shopping-bag" class="h-5 w-5" />
                    Basket
                </a>
            </div>
        </div>
    </div>

    @if($cartMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
             x-show="show" x-transition.opacity
             class="fixed top-4 right-4 z-50 flex items-center gap-2 bg-green-600 text-white text-sm px-4 py-3 border border-green-500 shadow-2xl">
            <flux:icon name="check-circle" class="h-4 w-4 flex-shrink-0" />
            {{ $cartMessage }}
        </div>
    @endif

    {{-- Category rail: every category from the database; Livewire updates products on click --}}
    <div class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-3 mb-2">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Browse by category</p>
                <span wire:loading.delay class="text-[10px] font-semibold uppercase tracking-wider text-brand-red">Updating…</span>
            </div>
            <div class="flex overflow-x-auto gap-0 scrollbar-hide border border-gray-200 dark:border-gray-800">
                <button type="button"
                        wire:key="shop-cat-tab-all"
                        wire:click="selectCategoryById"
                        class="flex-shrink-0 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide border-r border-gray-200 dark:border-gray-800 transition whitespace-nowrap cursor-pointer
                            {{ $categoryAllActive ? 'bg-gray-100 dark:bg-gray-900 text-brand-red' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-brand-red' }}">
                    All
                </button>
                @foreach($categories as $cat)
                    <button type="button"
                            wire:key="shop-cat-tab-{{ $cat->id }}"
                            wire:click="selectCategoryById({{ (int) $cat->id }})"
                            class="flex-shrink-0 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide border-r border-gray-200 dark:border-gray-800 last:border-r-0 transition whitespace-nowrap cursor-pointer
                                {{ $categoryRowActive($cat) ? 'bg-gray-100 dark:bg-gray-900 text-brand-red' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-brand-red' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center gap-3 mb-5 flex-wrap">

            <div x-data="{ open: false }" class="lg:hidden">
                <button type="button" @click="open = !open"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-800 dark:text-gray-200 hover:border-brand-red hover:text-brand-red transition">
                    <flux:icon name="adjustments-horizontal" class="h-4 w-4" />
                    Filters
                    @if($activeCategoryId !== null || $activeBrandId !== null || $categorySlugNorm !== '' || $brandSlugNorm !== '')
                        <span class="w-2 h-2 bg-brand-red" aria-hidden="true"></span>
                    @endif
                </button>

                <div x-show="open" x-transition.opacity
                     x-cloak style="display:none;"
                     class="fixed inset-0 z-40 flex">
                    <div class="absolute inset-0 bg-black/60" @click="open = false" aria-hidden="true"></div>
                    <div class="relative z-50 w-80 max-w-full bg-white dark:bg-gray-950 h-full overflow-y-auto border-r border-gray-200 dark:border-gray-800 shadow-2xl">
                        <div class="sticky top-0 z-10 flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white">Filter products</h3>
                            <button type="button" @click="open = false" class="text-gray-500 hover:text-gray-900 dark:hover:text-white p-1" aria-label="Close filters">
                                <flux:icon name="x-mark" class="h-5 w-5" />
                            </button>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <flux:input wire:model.live.debounce.400ms="search" placeholder="Search products…" icon="magnifying-glass" clearable />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Categories</p>
                                <div class="border border-gray-200 dark:border-gray-800 divide-y divide-gray-200 dark:divide-gray-800">
                                    <button type="button"
                                            wire:click="selectCategoryById"
                                            @click="setTimeout(() => { open = false }, 120)"
                                            class="w-full text-left text-sm px-3 py-2.5 font-medium transition cursor-pointer {{ $categoryAllActive ? 'bg-gray-50 dark:bg-gray-900 text-brand-red' : 'text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                                        All categories
                                    </button>
                                    @foreach($categories as $cat)
                                        <button type="button"
                                                wire:key="shop-cat-m-{{ $cat->id }}"
                                                wire:click="selectCategoryById({{ (int) $cat->id }})"
                                                @click="setTimeout(() => { open = false }, 120)"
                                                class="w-full text-left text-sm px-3 py-2.5 font-medium transition cursor-pointer {{ $categoryRowActive($cat) ? 'bg-gray-50 dark:bg-gray-900 text-brand-red' : 'text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                                            {{ $cat->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Brands</p>
                                <div class="border border-gray-200 dark:border-gray-800 divide-y divide-gray-200 dark:divide-gray-800">
                                    <button type="button"
                                            wire:click="selectBrandById"
                                            @click="setTimeout(() => { open = false }, 120)"
                                            class="w-full text-left text-sm px-3 py-2.5 font-medium transition cursor-pointer {{ $brandAllActive ? 'bg-gray-50 dark:bg-gray-900 text-brand-red' : 'text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                                        All brands
                                    </button>
                                    @foreach($brands as $brand)
                                        <button type="button"
                                                wire:key="shop-brand-m-{{ $brand->id }}"
                                                wire:click="selectBrandById({{ (int) $brand->id }})"
                                                @click="setTimeout(() => { open = false }, 120)"
                                                class="w-full text-left text-sm px-3 py-2.5 font-medium transition cursor-pointer {{ $brandRowActive($brand) ? 'bg-gray-50 dark:bg-gray-900 text-brand-red' : 'text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                                            {{ $brand->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @if($search || $activeCategoryId !== null || $activeBrandId !== null || $categorySlugNorm !== '' || $brandSlugNorm !== '')
                                <button type="button"
                                        wire:click="clearFilters"
                                        @click="setTimeout(() => { open = false }, 120)"
                                        class="w-full text-sm font-semibold text-brand-red border border-brand-red/40 py-2.5 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    Clear all filters
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 flex-1 min-w-[12rem]">
                <span wire:loading.remove>{{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}</span>
                <span wire:loading class="italic text-brand-red">Updating results…</span>
            </p>

            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 hidden sm:inline">Sort:</span>
                <flux:select wire:model.live="sort" class="text-sm min-w-[11rem]">
                    <flux:select.option value="newest">Newest</flux:select.option>
                    <flux:select.option value="price_low">Price: low → high</flux:select.option>
                    <flux:select.option value="price_high">Price: high → low</flux:select.option>
                    <flux:select.option value="name">Name A–Z</flux:select.option>
                </flux:select>
            </div>
        </div>

        @if($categoryName || $brandName || $search)
            <div class="flex flex-wrap gap-2 mb-5">
                @if($categoryName)
                    <span class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-sm px-3 py-1.5 text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-950">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Category</span>
                        {{ $categoryName }}
                        <button type="button" wire:click="selectCategoryById" class="text-gray-400 hover:text-brand-red leading-none text-lg" aria-label="Remove category filter">&times;</button>
                    </span>
                @endif
                @if($brandName)
                    <span class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-sm px-3 py-1.5 text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-950">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Brand</span>
                        {{ $brandName }}
                        <button type="button" wire:click="selectBrandById" class="text-gray-400 hover:text-brand-red leading-none text-lg" aria-label="Remove brand filter">&times;</button>
                    </span>
                @endif
                @if($search)
                    <span class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-600 text-sm px-3 py-1.5 text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-950">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Search</span>
                        “{{ $search }}”
                        <button type="button" wire:click="clearSearch" class="text-gray-400 hover:text-brand-red leading-none text-lg" aria-label="Clear search">&times;</button>
                    </span>
                @endif
            </div>
        @endif

        <div class="flex gap-8">

            <aside class="hidden lg:block w-72 flex-shrink-0 space-y-4">

                <div class="border border-gray-200 dark:border-gray-800 p-3 bg-white dark:bg-gray-950">
                    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search…" icon="magnifying-glass" clearable />
                </div>

                <div class="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950" x-data="{ openCat: true }">
                    <button type="button" @click="openCat = !openCat"
                            class="w-full flex items-center justify-between px-3 py-2.5 border-b border-gray-200 dark:border-gray-800 text-left">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Categories</span>
                        <span x-show="openCat" aria-hidden="true"><flux:icon name="chevron-up" class="h-3.5 w-3.5 text-gray-500" /></span>
                        <span x-show="!openCat" x-cloak style="display:none;" aria-hidden="true"><flux:icon name="chevron-down" class="h-3.5 w-3.5 text-gray-500" /></span>
                    </button>
                    <div x-show="openCat" x-transition class="max-h-[min(55vh,28rem)] overflow-y-auto">
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            <button type="button"
                                    wire:click="selectCategoryById"
                                    class="w-full text-left text-sm px-3 py-2 transition cursor-pointer {{ $categoryAllActive ? 'bg-gray-50 dark:bg-gray-900 text-brand-red font-semibold border-l-2 border-brand-red' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 border-l-2 border-transparent' }}">
                                All categories
                            </button>
                            @foreach($categories as $cat)
                                <button type="button"
                                        wire:key="shop-cat-side-{{ $cat->id }}"
                                        wire:click="selectCategoryById({{ (int) $cat->id }})"
                                        class="w-full text-left text-sm px-3 py-2 transition cursor-pointer {{ $categoryRowActive($cat) ? 'bg-gray-50 dark:bg-gray-900 text-brand-red font-semibold border-l-2 border-brand-red' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 border-l-2 border-transparent' }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950" x-data="{ openBrand: true }">
                    <button type="button" @click="openBrand = !openBrand"
                            class="w-full flex items-center justify-between px-3 py-2.5 border-b border-gray-200 dark:border-gray-800 text-left">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Brands</span>
                        <span x-show="openBrand" aria-hidden="true"><flux:icon name="chevron-up" class="h-3.5 w-3.5 text-gray-500" /></span>
                        <span x-show="!openBrand" x-cloak style="display:none;" aria-hidden="true"><flux:icon name="chevron-down" class="h-3.5 w-3.5 text-gray-500" /></span>
                    </button>
                    <div x-show="openBrand" x-transition class="max-h-[min(45vh,22rem)] overflow-y-auto">
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            <button type="button"
                                    wire:click="selectBrandById"
                                    class="w-full text-left text-sm px-3 py-2 transition cursor-pointer {{ $brandAllActive ? 'bg-gray-50 dark:bg-gray-900 text-brand-red font-semibold border-l-2 border-brand-red' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 border-l-2 border-transparent' }}">
                                All brands
                            </button>
                            @foreach($brands as $brand)
                                <button type="button"
                                        wire:key="shop-brand-side-{{ $brand->id }}"
                                        wire:click="selectBrandById({{ (int) $brand->id }})"
                                        class="w-full text-left text-sm px-3 py-2 transition cursor-pointer {{ $brandRowActive($brand) ? 'bg-gray-50 dark:bg-gray-900 text-brand-red font-semibold border-l-2 border-brand-red' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 border-l-2 border-transparent' }}">
                                    {{ $brand->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($search || $activeCategoryId !== null || $activeBrandId !== null || $categorySlugNorm !== '' || $brandSlugNorm !== '')
                    <button type="button" wire:click="clearFilters"
                            class="w-full text-xs font-bold uppercase tracking-wider text-brand-red border border-brand-red/50 py-2.5 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-center">
                        Clear all filters
                    </button>
                @endif
            </aside>

            <div class="flex-1 min-w-0">
                @if($products->isEmpty())
                    <div class="text-center py-20 border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <flux:icon name="magnifying-glass" class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                        <p class="text-gray-600 dark:text-gray-400 font-medium">No products match these filters.</p>
                        <button type="button" wire:click="clearFilters" class="mt-5 text-sm font-semibold text-brand-red border border-brand-red/50 px-4 py-2 hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                            Reset filters
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4"
                         wire:loading.class="opacity-50 pointer-events-none">
                        @foreach($products as $product)
                            <div wire:key="shop-product-{{ $product->slug }}-{{ $loop->index }}"
                                 class="group bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 hover:border-brand-red hover:shadow-lg transition-all flex flex-col">
                                <a href="{{ route('shop.product', $product->slug) }}" class="block overflow-hidden bg-gray-50 dark:bg-gray-900">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-36 sm:h-44 object-contain p-3 group-hover:opacity-90 transition duration-300"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-36 sm:h-44 flex items-center justify-center">
                                            <flux:icon name="photo" class="h-12 w-12 text-gray-300 dark:text-gray-600" />
                                        </div>
                                    @endif
                                </a>
                                <div class="p-4 flex flex-col flex-1 border-t border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-1">{{ $product->brand }}</p>
                                    <a href="{{ route('shop.product', $product->slug) }}"
                                       class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red transition line-clamp-2 flex-1 leading-snug">
                                        {{ $product->name }}
                                    </a>
                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                                            £{{ number_format($product->normal_price, 2) }}
                                        </span>
                                        @if($product->global_stock > 0)
                                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">In stock</span>
                                        @else
                                            <span class="text-xs text-red-500 font-medium">Out of stock</span>
                                        @endif
                                    </div>
                                    @if($product->global_stock > 0)
                                        <a href="{{ route('shop.product', $product->slug) }}"
                                           class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 bg-gray-900 dark:bg-gray-800 text-white text-xs font-semibold hover:bg-brand-red transition border border-transparent">
                                            View product
                                        </a>
                                    @else
                                        <a href="{{ route('shop.product', $product->slug) }}#stock-enquiry"
                                           class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 border border-brand-red text-brand-red text-xs font-semibold hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                            Enquire now
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
