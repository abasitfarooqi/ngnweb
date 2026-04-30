<div class="w-full max-w-md mx-auto px-4">
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <img class="h-12 w-auto mx-auto" src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors">
        </a>
        <h1 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Forgot your password?</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Enter your email and we will send you a reset link.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-8 sm:px-8">
        @if (session('status'))
            <div class="mb-5 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="sendResetLink" class="space-y-5">
            <div>
                <label for="email" class="ngn-label">Email address</label>
                <input id="email" type="email" wire:model.blur="email"
                    autocomplete="email" autofocus
                    class="ngn-input @error('email') border-red-500 @enderror"
                    placeholder="you@example.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="ngn-btn-primary w-full py-2.5 justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove>Send reset link</span>
                <span wire:loading>Sending...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-brand-red hover:text-red-700 transition">Back to sign in</a>
        </p>
    </div>
</div>
