<div>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Step 4: Confirmation --}}
    @if($step === 4)
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-5">
                <flux:icon name="check-circle" class="h-10 w-10 text-green-600" />
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Order Placed!</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-2">Your order <strong>#{{ $orderId }}</strong> has been confirmed.</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-8">
                A confirmation email will be sent to your registered address. We will contact you about delivery.
            </p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="{{ route('account.orders') }}"
                   class="bg-brand-red hover:bg-red-700 text-white font-semibold px-6 py-3 transition">
                    View My Orders
                </a>
                <a href="{{ route($continueShoppingRoute) }}"
                   class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Continue Shopping
                </a>
            </div>
        </div>

    @else
        {{-- Progress steps --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-5">Checkout</h1>
            <div class="flex items-center gap-0">
                @foreach(['Delivery', 'Shipping', 'Review'] as $i => $stepLabel)
                    @php $n = $i + 1; @endphp
                    <div class="flex items-center {{ $i > 0 ? 'flex-1' : '' }}">
                        @if($i > 0)
                            <div class="flex-1 h-0.5 {{ $step > $i ? 'bg-brand-red' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endif
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 flex items-center justify-center border-2 text-sm font-semibold flex-shrink-0
                                {{ $step > $n ? 'bg-brand-red border-brand-red text-white' : ($step === $n ? 'border-brand-red text-brand-red bg-white dark:bg-gray-900' : 'border-gray-300 dark:border-gray-600 text-gray-400 bg-white dark:bg-gray-900') }}">
                                @if($step > $n)
                                    <flux:icon name="check" class="h-4 w-4" />
                                @else
                                    {{ $n }}
                                @endif
                            </div>
                            <span class="text-xs mt-1 text-gray-500 dark:text-gray-400 hidden sm:block">{{ $stepLabel }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($errorMessage)
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 text-sm">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: step content --}}
            <div class="lg:col-span-2">

                {{-- STEP 1: Delivery Address --}}
                @if($step === 1)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Delivery Address</h2>

                        @if($addresses->isNotEmpty())
                            <div class="space-y-3 mb-5">
                                @foreach($addresses as $addr)
                                    <label class="flex items-start gap-3 p-4 border-2 cursor-pointer transition
                                        {{ $selectedAddressId === $addr->id ? 'border-brand-red bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                        <input type="radio" wire:model.live="selectedAddressId" value="{{ $addr->id }}"
                                               class="mt-0.5 accent-brand-red">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $addr->first_name }} {{ $addr->last_name }}
                                                @if($addr->is_default) <span class="ml-2 text-xs text-brand-red">(Default)</span> @endif
                                            </p>
                                            <p class="text-gray-500 dark:text-gray-400">{{ $addr->street_address }}</p>
                                            <p class="text-gray-500 dark:text-gray-400">{{ $addr->city }}, {{ $addr->postcode }}</p>
                                            @if($addr->phone_number) <p class="text-gray-500 dark:text-gray-400">{{ $addr->phone_number }}</p> @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <button wire:click="$toggle('newAddress')"
                                    class="text-sm text-brand-red hover:underline mb-4 flex items-center gap-1">
                                <flux:icon :name="$newAddress ? 'minus-circle' : 'plus-circle'" class="h-4 w-4" />
                                {{ $newAddress ? 'Cancel new address' : 'Add a new address' }}
                            </button>
                        @else
                            @php $newAddress = true; @endphp
                        @endif

                        @if($newAddress || $addresses->isEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="first_name">First Name</flux:label>
                                    <flux:input id="first_name" wire:model="first_name" />
                                    @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <flux:label for="last_name">Last Name</flux:label>
                                    <flux:input id="last_name" wire:model="last_name" />
                                    @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <flux:label for="street_address">Street Address</flux:label>
                                    <flux:input id="street_address" wire:model="street_address" />
                                    @error('street_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <flux:label for="street_address_plus">Apt / Suite (optional)</flux:label>
                                    <flux:input id="street_address_plus" wire:model="street_address_plus" />
                                </div>
                                <div>
                                    <flux:label for="city">City</flux:label>
                                    <flux:input id="city" wire:model="city" />
                                    @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <flux:label for="postcode">Postcode</flux:label>
                                    <flux:input id="postcode" wire:model="postcode" />
                                    @error('postcode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <flux:label for="phone_number">Phone Number</flux:label>
                                    <flux:input id="phone_number" wire:model="phone_number" type="tel" />
                                    @error('phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- STEP 2: Shipping Method --}}
                @if($step === 2)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Shipping Method</h2>
                        <div class="space-y-3">
                            @foreach($shippingMethods as $method)
                                <label class="flex items-start gap-3 p-4 border-2 cursor-pointer transition
                                    {{ $shippingMethodId === $method->id ? 'border-brand-red bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="shippingMethodId" value="{{ $method->id }}"
                                           class="mt-0.5 accent-brand-red">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $method->name }}</p>
                                            <p class="font-bold text-gray-900 dark:text-white">
                                                {{ $method->shipping_amount > 0 ? '£' . number_format($method->shipping_amount, 2) : 'Free' }}
                                            </p>
                                        </div>
                                        @if($method->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $method->description }}</p>
                                        @endif
                                        @if($method->in_store_pickup)
                                            <p class="text-xs text-green-600 mt-0.5">Store collection available</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('shippingMethodId') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- STEP 3: Review & Place Order --}}
                @if($step === 3)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Review Your Order</h2>

                        {{-- Items --}}
                        <div class="space-y-3 mb-6">
                            @foreach($items as $item)
                                <div class="flex items-center gap-3">
                            @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}"
                                             class="w-12 h-12 object-contain bg-gray-50 p-1 flex-shrink-0">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">{{ $item['product_name'] }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        £{{ number_format($item['line_total'], 2) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Payment method --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment method</p>
                            @if($paymentMethods->isEmpty())
                                <p class="text-sm text-amber-700 dark:text-amber-300">No payment methods are available. Please contact us.</p>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Pay with PayPal online, or choose cash when you collect at a branch.</p>
                                <div class="space-y-2">
                                    @foreach($paymentMethods as $method)
                                        <label class="flex items-center gap-3 text-sm cursor-pointer p-3 border-2 transition
                                            {{ (int) $paymentMethodId === (int) $method->id ? 'border-brand-red bg-red-50/60 dark:bg-red-950/20' : 'border-gray-200 dark:border-gray-600' }}">
                                            <input type="radio" wire:model.live="paymentMethodId" value="{{ $method->id }}"
                                                   class="accent-brand-red">
                                            <span class="text-gray-700 dark:text-gray-300">{{ $method->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Shipping selected --}}
                        @if($shippingMethod)
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 text-sm text-gray-600 dark:text-gray-400">
                                <p>Shipping: <strong class="text-gray-900 dark:text-white">{{ $shippingMethod->name }}</strong>
                                   — {{ $shippingCost > 0 ? '£' . number_format($shippingCost, 2) : 'Free' }}</p>
                            </div>
                        @endif

                        {{-- Place order --}}
                        <button wire:click="placeOrder"
                                wire:loading.attr="disabled"
                                class="mt-6 w-full bg-brand-red hover:bg-red-700 text-white font-bold py-4 transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="placeOrder">
                                <flux:icon name="lock-closed" class="inline h-5 w-5 mr-1" />
                                Place Order — £{{ number_format($grandTotal, 2) }}
                            </span>
                            <span wire:loading wire:target="placeOrder">Placing Order…</span>
                        </button>
                        <p class="text-xs text-center text-gray-500 mt-3">
                            By placing your order you agree to our
                            <a href="{{ route('site.terms') }}" target="_blank" class="text-brand-red hover:underline">Terms &amp; Conditions</a>.
                        </p>
                    </div>
                @endif

                {{-- Navigation buttons --}}
                @if(in_array($step, [1, 2]))
                    <div class="flex justify-between mt-4">
                        @if($step > 1)
                            <button wire:click="prevStep"
                                    class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
                                <flux:icon name="arrow-left" class="h-4 w-4" /> Back
                            </button>
                        @else
                            <a href="{{ route($basketRoute) }}"
                               class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
                                <flux:icon name="arrow-left" class="h-4 w-4" /> Back to Basket
                            </a>
                        @endif
                        <button wire:click="nextStep"
                                wire:loading.attr="disabled"
                                class="bg-brand-red hover:bg-red-700 text-white font-semibold px-6 py-2.5 transition">
                            <span wire:loading.remove wire:target="nextStep">Continue</span>
                            <span wire:loading wire:target="nextStep">Checking…</span>
                        </button>
                    </div>
                @elseif($step === 3)
                    <div class="mt-4">
                        <button wire:click="prevStep"
                                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
                            <flux:icon name="arrow-left" class="h-4 w-4" /> Back
                        </button>
                    </div>
                @endif
            </div>

            {{-- Right: order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 sticky top-24">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Summary</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        @foreach($items as $item)
                            <div class="flex justify-between">
                                <span class="truncate pr-2">{{ $item['product_name'] }} ×{{ $item['quantity'] }}</span>
                                <span class="flex-shrink-0">£{{ number_format($item['line_total'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>£{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span>{{ $shippingCost > 0 ? '£' . number_format($shippingCost, 2) : 'TBC' }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 dark:text-white text-base pt-1 border-t border-gray-200 dark:border-gray-700">
                            <span>Total</span>
                            <span>£{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
