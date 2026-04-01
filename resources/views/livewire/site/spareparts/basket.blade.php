<div>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Spareparts Basket</h1>
        <a href="{{ route('spareparts.index') }}" class="text-sm text-brand-red hover:underline">Continue browsing</a>
    </div>

    @if($isEmpty)
        <div class="text-center py-16 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Your basket is empty</h2>
            <a href="{{ route('spareparts.index') }}" class="inline-block bg-brand-red hover:bg-red-700 text-white font-semibold px-6 py-3 transition">
                Browse spareparts
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $index => $item)
                    @php
                        $itemUrl = $item['item_type'] === 'sparepart'
                            ? ($item['sparepart_url'] ?: route('spareparts.index'))
                            : route('shop.product', $item['slug']);
                    @endphp
                    <div class="flex gap-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4" wire:key="sp-item-{{ $item['row_id'] }}-{{ $index }}">
                        <a href="{{ $itemUrl }}" class="flex-shrink-0">
                            @if($item['image_url'])
                                <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}" class="w-20 h-20 object-contain bg-gray-50 dark:bg-gray-700 p-1">
                            @else
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700"></div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ $itemUrl }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red">{{ $item['product_name'] }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $item['item_type'] === 'sparepart' ? 'Part Number: ' . $item['part_number'] : 'SKU: ' . $item['sku'] }}
                            </p>
                            @if(!empty($item['variation']))
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item['variation'] }}</p>
                            @endif

                            <div class="flex items-center justify-between mt-3 gap-2">
                                <div class="flex items-center border border-gray-300 dark:border-gray-600">
                                    <button type="button" wire:click="decrementQuantityAt({{ $index }})" class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">−</button>
                                    <span class="w-8 text-center text-sm font-medium">{{ $item['quantity'] }}</span>
                                    <button type="button" wire:click="incrementQuantityAt({{ $index }})" class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">+</button>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-gray-900 dark:text-white">£{{ number_format($item['line_total'], 2) }}</span>
                                    <button type="button" wire:click="requestRemove({{ $index }})" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-end pt-2">
                    <button type="button" wire:click="requestClearBasket" class="text-xs text-gray-400 hover:text-red-500">Clear basket</button>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Order Summary</h2>
                    <div class="flex justify-between text-sm mb-4"><span>Subtotal</span><span>£{{ number_format($subtotal, 2) }}</span></div>
                    @auth('customer')
                        <a href="{{ route('spareparts.checkout') }}" class="block w-full bg-brand-red hover:bg-red-700 text-white text-center font-semibold py-3 transition mb-3">Proceed to Checkout</a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('spareparts.checkout')) }}" class="block w-full bg-brand-red hover:bg-red-700 text-white text-center font-semibold py-3 transition mb-3">Sign In to Checkout</a>
                    @endauth
                    <a href="{{ route('spareparts.index') }}" class="block w-full border border-gray-300 dark:border-gray-600 text-center text-sm font-medium text-gray-700 dark:text-gray-300 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Continue Browsing</a>
                </div>
            </div>
        </div>
    @endif
</div>

@if($pendingRemoveIndex !== null || $pendingClearAll)
    <div class="fixed inset-0 z-[80] flex items-center justify-center px-4" role="dialog" aria-modal="true">
        <button type="button"
                wire:click="cancelPendingRemoval"
                class="absolute inset-0 bg-black/50 border-0 cursor-default w-full h-full"
                aria-label="Dismiss"></button>
        <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm removal</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                @if($pendingClearAll)
                    Clear your entire basket?
                @else
                    Remove this item from your basket?
                @endif
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" wire:click="cancelPendingRemoval"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button" wire:click="confirmPendingRemoval"
                        class="px-4 py-2 bg-brand-red text-white text-sm font-semibold hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
@endif
</div>
