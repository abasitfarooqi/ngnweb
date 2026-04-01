<div>
{{-- Breadcrumb --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-500">
            <a href="/" class="hover:text-brand-red">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.home') }}" class="hover:text-brand-red">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 dark:text-gray-200">{{ $product['name'] }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- Images --}}
        <div>
            {{-- Main image --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 mb-3">
                @php
                    $images = $product['image_array'] ?: ($product['image_url'] ? [$product['image_url']] : []);
                @endphp
                @if(!empty($images))
                    <img src="{{ $images[$activeImage] ?? $images[0] }}"
                         alt="{{ $product['name'] }}"
                         class="w-full h-80 object-contain">
                @else
                    <div class="w-full h-80 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <flux:icon name="photo" class="h-16 w-16 text-gray-300" />
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if(count($images) > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($images as $idx => $img)
                        <button wire:click="setImage({{ $idx }})"
                                class="flex-shrink-0 w-16 h-16 border-2 {{ $activeImage === $idx ? 'border-brand-red' : 'border-gray-200 dark:border-gray-600' }} p-1 bg-white dark:bg-gray-800 transition">
                            <img src="{{ $img }}" alt="Image {{ $idx + 1 }}" class="w-full h-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product info --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">
                {{ $product['name'] }}
            </h1>

            @if($product['colour'])
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Colour: {{ $product['colour'] }}</p>
            @endif

            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                £{{ number_format($product['normal_price'], 2) }}
            </div>

            {{-- Stock badge --}}
            @if($availability['total_balance'] > 0)
                <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 dark:bg-green-900/20 px-3 py-1 text-sm mb-5">
                    <flux:icon name="check-circle" class="h-4 w-4" /> In Stock ({{ (int) $availability['total_balance'] }} available)
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-red-600 bg-red-50 dark:bg-red-900/20 px-3 py-1 text-sm mb-5">
                    <flux:icon name="x-circle" class="h-4 w-4" /> Out of Stock
                </span>
            @endif

            {{-- Variants --}}
            @if(count($product['variants']) > 1)
                <div class="mb-5">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select variant:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product['variants'] as $variant)
                            <button wire:click="selectVariant({{ $variant['id'] }})"
                                    class="px-3 py-1.5 text-sm border-2 transition
                                        {{ $selectedVariantId === $variant['id']
                                            ? 'border-brand-red text-brand-red bg-red-50 dark:bg-red-900/20'
                                            : 'border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red' }}">
                                {{ $variant['variation'] ?: $variant['sku'] }}
                                @if($variant['total_balance'] <= 0)
                                    <span class="ml-1 text-xs text-red-400">(OOS)</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @elseif(count($product['variants']) === 1)
                <input type="hidden" wire:model="selectedVariantId" value="{{ $product['variants'][0]['id'] }}">
            @endif

            {{-- Quantity --}}
            <div class="flex items-center gap-3 mb-5">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Qty:</p>
                <div class="flex items-center border border-gray-300 dark:border-gray-600">
                    <button wire:click="decrementQuantity"
                            @disabled($availability['total_balance'] <= 0)
                            class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-lg transition">−</button>
                    <span class="w-10 text-center font-medium text-gray-900 dark:text-white">{{ $quantity }}</span>
                    <button wire:click="incrementQuantity"
                            @disabled($availability['total_balance'] <= 0)
                            class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-lg transition">+</button>
                </div>
            </div>

            {{-- Cart message --}}
            @if($cartMessage)
                <div class="mb-4 px-4 py-3 text-sm {{ $cartError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                    {{ $cartMessage }}
                </div>
            @endif

            {{-- Add to cart --}}
            <div class="flex gap-3">
                <button wire:click="addToCart"
                        wire:loading.attr="disabled"
                        @disabled($availability['total_balance'] <= 0)
                        class="flex-1 bg-brand-red hover:bg-red-700 text-white font-semibold px-6 py-3 transition flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="addToCart">
                        <flux:icon name="shopping-bag" class="h-5 w-5 inline mr-1" />
                        {{ $availability['total_balance'] > 0 ? 'Add to Basket' : 'Out of Stock' }}
                    </span>
                    <span wire:loading wire:target="addToCart">Adding…</span>
                </button>
                <a href="{{ route('shop.basket') }}"
                   class="border border-gray-300 dark:border-gray-600 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition text-sm font-medium text-gray-700 dark:text-gray-300">
                    View Basket
                </a>
            </div>

            @if($availability['total_balance'] <= 0)
                <div id="stock-enquiry" class="mt-6">
                    @include('livewire.site.partials.sales.enquiry-form', [
                        'submitAction' => 'submitEnquiry',
                        'heading' => 'Stock Enquiry',
                        'enquiryTypeLabel' => 'Shop',
                        'showRegNo' => false,
                        'submitButtonLabel' => 'Send stock enquiry',
                    ])
                </div>
            @endif

            {{-- Trust badges --}}
            <div class="mt-6 grid grid-cols-2 gap-3">
                @foreach([
                    ['icon' => 'truck', 'text' => 'Free delivery over £100'],
                    ['icon' => 'arrow-uturn-left', 'text' => '30-day returns'],
                    ['icon' => 'shield-check', 'text' => 'Secure checkout'],
                    ['icon' => 'phone', 'text' => 'Expert support'],
                ] as $badge)
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <flux:icon name="{{ $badge['icon'] }}" class="h-4 w-4 text-brand-red flex-shrink-0" />
                        {{ $badge['text'] }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product['description'])
        <div class="mt-12">
            <flux:accordion>
                <flux:accordion.item heading="Product Description" :expanded="true">
                    <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-400">
                        {!! nl2br(e($product['description'])) !!}
                    </div>
                </flux:accordion.item>
                @if($product['extended_description'])
                    <flux:accordion.item heading="Additional Information">
                        <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-400">
                            {!! nl2br(e($product['extended_description'])) !!}
                        </div>
                    </flux:accordion.item>
                @endif
                @if(!empty($availability['branches']) && $availability['branches']->count() > 0)
                    <flux:accordion.item heading="Stock by Branch">
                        <div class="space-y-2">
                            @foreach($availability['branches'] as $branch)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $branch->branch_name }}</span>
                                    <span class="{{ $branch->branch_balance > 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                                        {{ max(0, (int) $branch->branch_balance) }} in stock
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </flux:accordion.item>
                @endif
            </flux:accordion>
        </div>
    @endif
</div>
</div>
