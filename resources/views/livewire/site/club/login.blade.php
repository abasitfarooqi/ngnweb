<div>
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-12 w-auto mx-auto mb-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">NGN Club Login</h2>
            <p class="text-gray-600 dark:text-gray-400">Access your member dashboard</p>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle" class="mb-6">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-8">
            @if(!$otpSent)
                <form wire:submit="sendOtp" class="space-y-5">
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="your@email.com" />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">Send Verification Code</flux:button>
                </form>
            @else
                <form wire:submit="verifyOtp" class="space-y-5">
                    <flux:field>
                        <flux:label>Enter 6-Digit Code</flux:label>
                        <flux:input wire:model="otp" type="text" placeholder="000000" maxlength="6" class="text-center text-2xl tracking-widest" />
                        <flux:error name="otp" />
                        <flux:description>Code sent to {{ $email }}</flux:description>
                    </flux:field>
                    <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">Verify & Login</flux:button>
                    <button type="button" wire:click="$set('otpSent', false)" class="w-full text-sm text-gray-600 dark:text-gray-400 hover:text-brand-red transition">
                        Use different email
                    </button>
                </form>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Not a member? <a href="/club" class="text-brand-red hover:underline font-medium">Join Now</a>
                </p>
            </div>
        </flux:card>
    </div>
</div>
</div>
