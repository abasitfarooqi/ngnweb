<div class="w-full max-w-lg mx-auto px-4">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <img class="h-12 w-auto mx-auto" src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors">
        </a>
        <h1 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Create your account</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-brand-red hover:text-red-700 transition">Sign in</a>
        </p>
    </div>

    {{-- Progress bar --}}
    <div class="mb-6">
        <div class="flex items-center">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex items-center {{ $i < $totalSteps ? 'flex-1' : '' }}">
                    <div class="flex items-center justify-center w-9 h-9 border-2 text-sm font-semibold flex-shrink-0
                        {{ $currentStep >= $i
                            ? 'bg-brand-red border-brand-red text-white'
                            : 'border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800' }}">
                        @if($currentStep > $i)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    @if ($i < $totalSteps)
                        <div class="flex-1 h-0.5 mx-1 {{ $currentStep > $i ? 'bg-brand-red' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endfor
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
            <span>Account</span>
            <span>Verify</span>
            <span>Profile</span>
        </div>
    </div>

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-8 sm:px-8">

        @if (session('message'))
            <div class="mb-5 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
                {{ session('message') }}
            </div>
        @endif

        {{-- STEP 1: Account Basics --}}
        @if ($currentStep === 1)
            <div class="space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Account details</h2>

                <div>
                    <label for="email" class="ngn-label">Email address</label>
                    <input id="email" type="email" wire:model.blur="email"
                        autocomplete="email"
                        class="ngn-input @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                    @error('email') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="ngn-label">Phone number</label>
                    <input id="phone" type="tel" wire:model.blur="phone"
                        autocomplete="tel"
                        class="ngn-input @error('phone') border-red-500 @enderror"
                        placeholder="07XXXXXXXXX">
                    @error('phone') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="ngn-label">Password</label>
                    <input id="password" type="password" wire:model.blur="password"
                        autocomplete="new-password"
                        class="ngn-input @error('password') border-red-500 @enderror">
                    @error('password') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="ngn-label">Confirm password</label>
                    <input id="password_confirmation" type="password" wire:model.blur="password_confirmation"
                        autocomplete="new-password"
                        class="ngn-input">
                </div>

                <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                    <input type="checkbox" wire:model="terms" class="mt-0.5 accent-brand-red flex-shrink-0">
                    <span>
                        I agree to the
                        <a href="/legals/terms-conditions" target="_blank" class="text-brand-red hover:underline">Terms &amp; Conditions</a>
                        and
                        <a href="/legals/privacy-policy" target="_blank" class="text-brand-red hover:underline">Privacy Policy</a>
                    </span>
                </label>
                @error('terms') <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <button type="button" wire:click="nextStep"
                    class="ngn-btn-primary w-full py-2.5 justify-center"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="nextStep">Continue</span>
                    <span wire:loading wire:target="nextStep">Checking…</span>
                </button>
            </div>
        @endif

        {{-- STEP 2: Email Verification --}}
        @if ($currentStep === 2)
            <div class="text-center space-y-5">
                <div class="mx-auto flex items-center justify-center h-16 w-16 bg-green-100 dark:bg-green-900/30">
                    <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Check your email</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        We'll send a verification link to
                        <span class="font-medium text-gray-900 dark:text-white">{{ $email }}</span>
                        after completing registration.
                    </p>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-500">
                    You can also verify from your account dashboard after sign-up.
                </p>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="previousStep"
                        class="ngn-btn-secondary flex-1 py-2.5 justify-center">
                        Back
                    </button>
                    <button type="button" wire:click="nextStep"
                        class="ngn-btn-primary flex-1 py-2.5 justify-center">
                        Continue
                    </button>
                </div>
            </div>
        @endif

        {{-- STEP 3: Profile --}}
        @if ($currentStep === 3)
            <div class="space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your profile <span class="text-sm font-normal text-gray-400">(optional)</span></h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="ngn-label">First name</label>
                        <input id="first_name" type="text" wire:model.blur="first_name"
                            autocomplete="given-name"
                            class="ngn-input @error('first_name') border-red-500 @enderror">
                        @error('first_name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="ngn-label">Last name</label>
                        <input id="last_name" type="text" wire:model.blur="last_name"
                            autocomplete="family-name"
                            class="ngn-input @error('last_name') border-red-500 @enderror">
                        @error('last_name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="postcode" class="ngn-label">Postcode <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input id="postcode" type="text" wire:model.blur="postcode"
                            autocomplete="postal-code"
                            class="ngn-input"
                            placeholder="SW1A 1AA">
                    </div>
                    <div>
                        <label for="city" class="ngn-label">City <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input id="city" type="text" wire:model.blur="city"
                            autocomplete="address-level2"
                            class="ngn-input"
                            placeholder="London">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="previousStep"
                        class="ngn-btn-secondary flex-1 py-2.5 justify-center">
                        Back
                    </button>
                    <button type="button" wire:click="completeRegistration"
                        class="ngn-btn-primary flex-1 py-2.5 justify-center"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="completeRegistration">Complete registration</span>
                        <span wire:loading wire:target="completeRegistration" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Creating account…
                        </span>
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
