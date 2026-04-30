<div class="w-full max-w-md mx-auto px-4">
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <img class="h-12 w-auto mx-auto" src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors">
        </a>
        <h1 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Reset your password</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-8 sm:px-8">
        <form wire:submit="resetPassword" class="space-y-5">
            <input type="hidden" wire:model="token">

            <div>
                <label for="email" class="ngn-label">Email address</label>
                <input id="email" type="email" wire:model.blur="email"
                    autocomplete="username"
                    class="ngn-input @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="ngn-label">New password</label>
                <input id="password" type="password" wire:model.blur="password"
                    autocomplete="new-password"
                    class="ngn-input @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="ngn-label">Confirm new password</label>
                <input id="password_confirmation" type="password" wire:model.blur="password_confirmation"
                    autocomplete="new-password"
                    class="ngn-input">
            </div>

            <button type="submit"
                class="ngn-btn-primary w-full py-2.5 justify-center"
                wire:loading.attr="disabled"
                wire:target="resetPassword">
                <span wire:loading.remove wire:target="resetPassword">Reset password</span>
                <span wire:loading wire:target="resetPassword" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Resetting…
                </span>
            </button>
        </form>
    </div>
</div>
