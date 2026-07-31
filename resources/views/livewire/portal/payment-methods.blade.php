<div class="space-y-6 max-w-3xl">
    <flux:heading size="xl">Payment Methods</flux:heading>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        We accept the following payment methods in store and online where shown at checkout. This page is for information only.
    </p>

    @if($methods->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="credit-card" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500 dark:text-gray-400">Payment methods will be available soon.</p>
        </flux:card>
    @else
        <x-site.form-grid :cols="2">
            @foreach($methods as $method)
                @php
                    $logo = trim((string) ($method->logo ?? ''));
                    $hasLogo = $logo !== '' && $logo !== '-';
                    $instructions = trim((string) ($method->instructions ?? ''));
                    $hasInstructions = $instructions !== '' && $instructions !== '-';
                @endphp
                <flux:card class="p-5">
                    <div class="flex items-start gap-4">
                        @if($hasLogo)
                            <img src="{{ $logo }}" alt="{{ $method->title }}" class="h-10 w-auto object-contain flex-shrink-0">
                        @else
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <flux:icon name="credit-card" class="h-5 w-5 text-gray-400" />
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $method->title }}</p>
                            @if($hasInstructions)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $instructions }}</p>
                            @endif
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </x-site.form-grid>
    @endif

    <flux:callout icon="shield-check" color="green">
        <flux:callout.heading>Secure Payments</flux:callout.heading>
        <flux:callout.text>
            All transactions are encrypted and processed securely. NGN Motors does not store card details.
        </flux:callout.text>
    </flux:callout>
</div>
