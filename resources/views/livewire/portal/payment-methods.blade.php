<div class="space-y-6 max-w-3xl">
    <flux:heading size="xl">Payment Methods</flux:heading>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        We currently accept the following payment methods. All payments are processed securely.
    </p>

    @if($methods->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="credit-card" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500 dark:text-gray-400">Payment methods will be available soon.</p>
        </flux:card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($methods as $method)
                <flux:card class="p-5">
                    <div class="flex items-start gap-4">
                        @if($method->logo)
                            <img src="{{ $method->logo }}" alt="{{ $method->title }}" class="h-10 w-auto object-contain flex-shrink-0">
                        @else
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <flux:icon name="credit-card" class="h-5 w-5 text-gray-400" />
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $method->title }}</p>
                            @if($method->instructions)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $method->instructions }}</p>
                            @endif
                            @if($method->link_url)
                                <a href="{{ $method->link_url }}" target="_blank"
                                   class="text-xs text-brand-red hover:underline mt-1 inline-block">
                                    Learn more →
                                </a>
                            @endif
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <flux:callout icon="shield-check" color="green">
        <flux:callout.heading>Secure Payments</flux:callout.heading>
        <flux:callout.text>
            All transactions are encrypted and processed securely. NGN Motors does not store card details.
        </flux:callout.text>
    </flux:callout>
</div>
