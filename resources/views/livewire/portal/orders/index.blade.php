<div class="space-y-6">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">Orders</flux:heading>
        <flux:button href="/shop" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Visit Shop</flux:button>
    </div>

    @if($orders->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="shopping-bag" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400 mb-4">You haven't placed any orders yet.</p>
            <flux:button href="/shop" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Browse Shop</flux:button>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <flux:card class="p-6">
                    <div class="flex justify-between items-start mb-4 gap-4 flex-wrap">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order #{{ $order->id }}</h3>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <flux:badge color="{{ match(strtolower($order->order_status ?? 'pending')) {
                            'pending' => 'yellow',
                            'in progress' => 'blue',
                            'confirmed' => 'blue',
                            'ready to collect' => 'purple',
                            'delivered' => 'green',
                            'cancelled' => 'red',
                            default => 'zinc'
                        } }}">
                            {{ $order->order_status ?? 'Pending' }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
                        <div>
                            <p class="text-gray-500">Total Amount</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">£{{ number_format($order->grand_total ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Payment</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_status ?? 'Pending') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Delivery Method</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->shippingMethod?->name ?? 'Standard' }}</p>
                        </div>
                    </div>

                    @if($order->items && $order->items->count() > 0)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mb-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Items</p>
                            <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                @foreach($order->items->take(3) as $item)
                                    <li>{{ $item->name ?? $item->product_name ?? 'Item' }} (×{{ $item->quantity ?? 1 }})</li>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <li class="text-gray-500">…and {{ $order->items->count() - 3 }} more items</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <div class="flex gap-3 flex-wrap">
                        <flux:button href="{{ route('account.orders.show', $order->id) }}" variant="outline" size="sm">View Details</flux:button>
                        @if(!in_array($order->order_status, ['Cancelled', 'Delivered']))
                            <flux:button href="mailto:support@neguinhomotors.co.uk?subject=Order+%23{{ $order->id }}" variant="ghost" size="sm">Contact Support</flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</div>
