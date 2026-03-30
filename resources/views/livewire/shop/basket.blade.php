<div>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Your Basket</h1>
        <a href="{{ route('shop.home') }}" class="text-sm text-brand-red hover:underline flex items-center gap-1">
            <flux:icon name="arrow-left" class="h-4 w-4" /> Continue Shopping
        </a>
    </div>

    @if($isEmpty)
        <div class="text-center py-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <flux:icon name="shopping-bag" class="h-16 w-16 text-gray-300 mx-auto mb-4" />
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Your basket is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Browse our range of motorcycle parts and accessories.</p>
            <a href="{{ route('shop.home') }}"
               class="inline-block bg-brand-red hover:bg-red-700 text-white font-semibold px-6 py-3 transition">
                Browse Shop
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Items list --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="flex gap-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4"
                         wire:key="item-{{ $item['row_id'] }}">
                        {{-- Image --}}
                        @php
                            $itemUrl = $item['item_type'] === 'sparepart'
                                ? ($item['sparepart_url'] ?: route('spareparts.index'))
                                : route('shop.product', $item['slug']);
                        @endphp
                        <a href="{{ $itemUrl }}" class="flex-shrink-0">
                            @if($item['image_url'])
                                <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}"
                                     class="w-20 h-20 object-contain bg-gray-50 dark:bg-gray-700 p-1">
                            @else
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <flux:icon name="photo" class="h-8 w-8 text-gray-300" />
                                </div>
                            @endif
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ $itemUrl }}"
                               class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red line-clamp-2">
                                {{ $item['product_name'] }}
                            </a>
                            @if($item['variation'])
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item['variation'] }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">SKU: {{ $item['sku'] }}</p>

                            <div class="flex items-center justify-between mt-3 flex-wrap gap-2">
                                {{-- Qty control --}}
                                <div class="flex items-center border border-gray-300 dark:border-gray-600">
                                    <button wire:click="updateQuantity('{{ $item['row_id'] }}', {{ max(1, $item['quantity'] - 1) }})"
                                            class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 text-base transition">−</button>
                                    <span class="w-8 text-center text-sm font-medium">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity('{{ $item['row_id'] }}', {{ min(100, $item['quantity'] + 1) }})"
                                            class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 text-base transition">+</button>
                                </div>

                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        £{{ number_format($item['line_total'], 2) }}
                                    </span>
                                    <button wire:click="remove('{{ $item['row_id'] }}')"
                                            wire:confirm="Remove this item?"
                                            class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end pt-2">
                    <button wire:click="clear" wire:confirm="Clear your entire basket?"
                            class="text-xs text-gray-400 hover:text-red-500 hover:underline">
                        Clear basket
                    </button>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Order Summary</h2>

                    <div class="space-y-3 text-sm mb-5">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>£{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between font-bold text-gray-900 dark:text-white text-base">
                            <span>Estimated Total</span>
                            <span>£{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    @auth('customer')
                        <a href="{{ route('shop.checkout') }}"
                           class="block w-full bg-brand-red hover:bg-red-700 text-white text-center font-semibold py-3 transition mb-3">
                            Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('shop.checkout')) }}"
                           class="block w-full bg-brand-red hover:bg-red-700 text-white text-center font-semibold py-3 transition mb-3">
                            Sign In to Checkout
                        </a>
                        <p class="text-xs text-center text-gray-500 dark:text-gray-400 mb-3">
                            No account?
                            <a href="{{ route('register') }}" class="text-brand-red hover:underline">Register free</a>
                        </p>
                    @endauth

                    <a href="{{ route('shop.home') }}"
                       class="block w-full border border-gray-300 dark:border-gray-600 text-center text-sm font-medium text-gray-700 dark:text-gray-300 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Continue Shopping
                    </a>

                    {{-- Trust --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                        @foreach([
                            ['icon' => 'shield-check', 'text' => 'Secure checkout'],
                            ['icon' => 'arrow-uturn-left', 'text' => '30-day returns'],
                            ['icon' => 'truck', 'text' => 'Free delivery over £100'],
                        ] as $t)
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <flux:icon name="{{ $t['icon'] }}" class="h-4 w-4 text-brand-red flex-shrink-0" />
                                {{ $t['text'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
