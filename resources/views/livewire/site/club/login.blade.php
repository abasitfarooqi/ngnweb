<div>
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        {{-- Logo & heading --}}
        <div class="text-center mb-8">
            <img src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors" class="h-12 w-auto mx-auto mb-4">
            <div class="inline-flex items-center gap-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-4 py-1 mb-4 text-sm font-semibold tracking-wide">
                ★ NGN CLUB
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Member Login</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Use your phone number and passkey to access your dashboard</p>
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle" class="mb-5">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-8">
            <form wire:submit="login" class="space-y-5">
                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="phone" type="tel" placeholder="07700 900 123" autocomplete="tel" />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label>Passkey</flux:label>
                    <flux:input wire:model="passkey" type="password" placeholder="Your passkey" autocomplete="current-password" />
                    <flux:error name="passkey" />
                    <flux:description>
                        Forgot your passkey?
                        <a href="{{ route('ngnclub.forgot') }}" class="text-brand-red hover:underline">Reset it here</a>
                    </flux:description>
                </flux:field>

                <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-red-700 font-semibold" wire:loading.attr="disabled">
                    <span wire:loading.remove>Login to My Club</span>
                    <span wire:loading>Logging in…</span>
                </flux:button>
            </form>

            {{-- Member links --}}
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3 text-center text-sm text-gray-600 dark:text-gray-400">
                <p>Not a member yet?
                    <a href="{{ route('ngnclub.register') }}" class="text-brand-red hover:underline font-medium">Join NGN Club for free →</a>
                </p>
            </div>
        </flux:card>

        {{-- Staff login --}}
        <div class="mt-6 text-center">
            <button wire:click="loginWithStaff" type="button"
                class="text-xs text-gray-400 dark:text-gray-600 hover:text-brand-red dark:hover:text-brand-red transition">
                Staff / Admin? Login here
            </button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 transition">← Back to main site</a>
        </div>
    </div>
</div>
</div>
