<div>
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-12 w-auto mx-auto mb-4">
            <div class="inline-flex items-center gap-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-4 py-1 mb-4 text-sm font-semibold tracking-wide">
                ★ NGN CLUB
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Forgot passkey</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Enter the <strong class="text-gray-700 dark:text-gray-300">mobile number</strong> or <strong class="text-gray-700 dark:text-gray-300">email address</strong> on your membership. We will send a 6-digit code by SMS or email, then you can set a new passkey.
            </p>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif
        @if (session('error'))
            <flux:callout variant="danger" icon="exclamation-circle" class="mb-5">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-8">
            @if (! $codeSent)
                <form wire:submit.prevent="sendCode" class="space-y-5">
                    <flux:field>
                        <flux:label>Phone or email</flux:label>
                        <flux:input
                            wire:model="identifier"
                            type="text"
                            autocomplete="username"
                            placeholder="07700900123 or you@example.com"
                        />
                        <flux:description>Use the same phone or email you registered with NGN Club.</flux:description>
                        <flux:error name="identifier" />
                    </flux:field>

                    <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-red-700 font-semibold" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="sendCode">Send verification code</span>
                        <span wire:loading wire:target="sendCode">Sending…</span>
                    </flux:button>
                </form>
            @else
                <form wire:submit.prevent="resetPasskey" class="space-y-5">
                    <flux:callout variant="info" icon="information-circle" class="text-sm">
                        <flux:callout.text>
                            Enter the 6-digit code, then press <strong class="text-gray-900 dark:text-white">Reset passkey</strong>. We will send your new passkey by SMS and by email when we hold both on file.
                            @if($continueToken !== '')
                                <span class="block mt-2 text-gray-600 dark:text-gray-400">You opened a valid reset link — complete the step below.</span>
                            @endif
                        </flux:callout.text>
                    </flux:callout>

                    <flux:field>
                        <flux:label>Verification code</flux:label>
                        <flux:input
                            wire:model="verification_code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            pattern="[0-9]*"
                            placeholder="000000"
                            autocomplete="one-time-code"
                        />
                        <flux:error name="verification_code" />
                    </flux:field>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 bg-brand-red px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-60"
                        wire:loading.attr="disabled"
                        wire:target="resetPasskey"
                    >
                        <span wire:loading.remove wire:target="resetPasskey">Reset passkey</span>
                        <span wire:loading wire:target="resetPasskey">Resetting…</span>
                    </button>

                    <div class="text-center">
                        <button type="button" wire:click="startOver" class="text-sm text-brand-red hover:underline">
                            Use a different phone or email
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('ngnclub.login') }}" class="text-brand-red hover:underline font-medium">← Back to club login</a>
            </div>
        </flux:card>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">← Back to main site</a>
        </div>
    </div>
</div>
</div>
