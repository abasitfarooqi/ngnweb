<div class="space-y-6 max-w-4xl">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <a href="{{ route('account.orders') }}"
               class="text-sm text-gray-500 hover:text-brand-red flex items-center gap-1 mb-1">
                <flux:icon name="arrow-left" class="h-4 w-4" /> Back to Orders
            </a>
            <flux:heading size="xl">Order #{{ $order->id }}</flux:heading>
            <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('d F Y \a\t H:i') }}</p>
        </div>
        @php
            $statusColour = match(strtolower($order->order_status)) {
                'confirmed' => 'green',
                'in progress' => 'blue',
                'ready to collect' => 'purple',
                'delivered' => 'green',
                'cancelled' => 'red',
                default => 'yellow',
            };
        @endphp
        <div class="flex items-center gap-2">
            <flux:badge color="{{ $statusColour }}" size="lg">{{ $order->order_status }}</flux:badge>
            @if(!in_array(strtolower((string) $order->order_status), ['cancelled', 'delivered'], true))
                <flux:button
                    wire:click="cancelOrder"
                    wire:confirm="Cancel this order?"
                    variant="ghost"
                    size="sm"
                    class="text-red-600">
                    Cancel order
                </flux:button>
            @endif
        </div>
    </div>

    @if($statusMessage)
        <flux:callout variant="{{ str_contains(strtolower($statusMessage), 'success') ? 'success' : 'warning' }}" icon="information-circle">
            <flux:callout.text>{{ $statusMessage }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Items --}}
    <flux:card class="p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Items Ordered</h2>
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4">
                    @if($item->product?->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}"
                             class="w-14 h-14 object-contain bg-gray-50 dark:bg-gray-700 p-1 flex-shrink-0">
                    @else
                        <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="photo" class="h-6 w-6 text-gray-300" />
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">SKU: {{ $item->sku }} · Qty: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-white">£{{ number_format($item->line_total, 2) }}</p>
                        <p class="text-xs text-gray-400">£{{ number_format($item->unit_price, 2) }} each</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="border-t border-gray-200 dark:border-gray-700 mt-5 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>£{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Shipping</span>
                <span>£{{ number_format($order->shipping_cost, 2) }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Discount</span>
                    <span>−£{{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-gray-900 dark:text-white text-base pt-2 border-t border-gray-200 dark:border-gray-700">
                <span>Grand Total</span>
                <span>£{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>
    </flux:card>

    {{-- Delivery & Payment --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <flux:card class="p-5">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <flux:icon name="truck" class="h-5 w-5 text-brand-red" /> Delivery Details
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                {{ $order->shippingMethod?->name ?? 'Standard Shipping' }}
            </p>
            @if($order->customerAddress)
                @php $addr = $order->customerAddress; @endphp
                <address class="text-sm text-gray-500 dark:text-gray-400 not-italic mt-2 space-y-0.5">
                    <p>{{ $addr->first_name }} {{ $addr->last_name }}</p>
                    <p>{{ $addr->street_address }}</p>
                    @if(!empty($addr->street_address_plus) && $addr->street_address_plus !== '-')
                        <p>{{ $addr->street_address_plus }}</p>
                    @endif
                    <p>{{ $addr->city }}, {{ $addr->postcode }}</p>
                </address>
            @endif
            <p class="text-xs mt-2 {{ $order->shipping_status === 'delivered' ? 'text-green-600' : 'text-yellow-600' }}">
                Shipping: {{ ucfirst($order->shipping_status) }}
            </p>
        </flux:card>

        <flux:card class="p-5">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <flux:icon name="credit-card" class="h-5 w-5 text-brand-red" /> Payment
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                {{ $order->paymentMethod?->title ?? 'Pending' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Status: {{ ucfirst($order->payment_status) }}</p>
            @if($order->payment_reference)
                <p class="text-xs text-gray-400 mt-1">Ref: {{ $order->payment_reference }}</p>
            @endif
        </flux:card>
    </div>

    {{-- Need help? --}}
    <flux:callout icon="information-circle" color="blue">
        <flux:callout.heading>Need help with this order?</flux:callout.heading>
        <flux:callout.text>
            Contact us at <a href="mailto:support@neguinhomotors.co.uk" class="underline">support@neguinhomotors.co.uk</a>
            quoting order #{{ $order->id }}.
        </flux:callout.text>
    </flux:callout>
</div>
