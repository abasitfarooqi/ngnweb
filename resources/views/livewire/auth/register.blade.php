<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <a href="/" class="flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="NGN Motor">
            </a>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900 dark:text-white">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Or
                <a href="{{ route('login') }}" class="font-medium text-brand-red hover:text-red-700">
                    sign in to existing account
                </a>
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md px-8 py-6">
            {{-- Progress Steps --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    @for ($i = 1; $i <= $totalSteps; $i++)
                        <div class="flex items-center {{ $i < $totalSteps ? 'flex-1' : '' }}">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 
                                {{ $currentStep >= $i ? 'bg-brand-red border-brand-red text-white' : 'border-gray-300 text-gray-500 dark:border-gray-600 dark:text-gray-400' }}">
                                {{ $i }}
                            </div>
                            @if ($i < $totalSteps)
                                <div class="flex-1 h-1 mx-2 {{ $currentStep > $i ? 'bg-brand-red' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                            @endif
                        </div>
                    @endfor
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-600 dark:text-gray-400">Account</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Profile</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Complete</span>
                </div>
            </div>

            {{-- Step 1: Account (no DB write) --}}
            @if ($currentStep === 1)
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email address</label>
                        <input type="email" wire:model="email" id="email" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone number</label>
                        <input type="tel" wire:model="phone" id="phone" placeholder="07XXXXXXXXX"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" wire:model="password" id="password" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input type="password" wire:model="password_confirmation" id="password_confirmation" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" wire:model="terms" id="terms" 
                            class="h-4 w-4 text-brand-red focus:ring-brand-red border-gray-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            I agree to the <a href="/legals/terms-conditions" target="_blank" class="text-brand-red hover:underline">Terms & Conditions</a> and <a href="/legals/privacy-policy" target="_blank" class="text-brand-red hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

                    <button type="button" wire:click="nextStep" 
                        class="w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-brand-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red">
                        Continue
                    </button>
                </div>
            @endif

            {{-- Step 2: Profile (no DB write) --}}
            @if ($currentStep === 2)
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First name</label>
                            <input type="text" wire:model="first_name" id="first_name" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                            @error('first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last name</label>
                            <input type="text" wire:model="last_name" id="last_name" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                            @error('last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="postcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postcode (optional)</label>
                            <input type="text" wire:model="postcode" id="postcode" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City (optional)</label>
                            <input type="text" wire:model="city" id="city" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" wire:click="previousStep" 
                            class="flex-1 py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Back
                        </button>
                        <button type="button" wire:click="nextStep" 
                            class="flex-1 py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-brand-red hover:bg-red-700">
                            Continue
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 3: Complete (single full insert of auth + profile) --}}
            @if ($currentStep === 3)
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Review your details and create your account. We will create your login and profile in one step.
                    </p>
                    <dl class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <div><span class="font-medium">Email:</span> {{ $email }}</div>
                        <div><span class="font-medium">Phone:</span> {{ $phone }}</div>
                        <div><span class="font-medium">Name:</span> {{ $first_name }} {{ $last_name }}</div>
                        @if ($city || $postcode)
                            <div><span class="font-medium">Address:</span> {{ $city }}{{ $city && $postcode ? ', ' : '' }}{{ $postcode }}</div>
                        @endif
                    </dl>
                    <div class="flex gap-4">
                        <button type="button" wire:click="previousStep" 
                            class="flex-1 py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Back
                        </button>
                        <button type="button" wire:click="completeRegistration" 
                            class="flex-1 py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-brand-red hover:bg-red-700">
                            Complete Registration
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
