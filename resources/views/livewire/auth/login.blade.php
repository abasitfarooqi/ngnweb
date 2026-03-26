<div class="w-full max-w-md mx-auto px-4">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <img class="h-12 w-auto mx-auto" src="{{ asset('img/ngn-motor-logo-fit-optimized.png') }}" alt="NGN Motors">
        </a>
        <h1 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Sign in to your account</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-brand-red hover:text-red-700 transition">Create one</a>
        </p>
    </div>

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-8 sm:px-8">

        @if (session('status'))
            <div class="mb-5 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">

            {{-- Email --}}
            <div>
                <label for="email" class="ngn-label">Email address</label>
                <input id="email" type="email" wire:model.blur="email"
                    autocomplete="username"
                    class="ngn-input @error('email') border-red-500 dark:border-red-500 @enderror"
                    placeholder="you@example.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="ngn-label">Password</label>
                <input id="password" type="password" wire:model.blur="password"
                    autocomplete="current-password"
                    class="ngn-input @error('password') border-red-500 dark:border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + forgot --}}
            <div class="flex items-center justify-between gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="accent-brand-red">
                    <span>Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-brand-red hover:text-red-700 transition whitespace-nowrap">
                        Forgot password?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="ngn-btn-primary w-full py-2.5 justify-center"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Sign in</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Signing in…
                </span>
            </button>

        </form>

        {{-- NGN Club login shortcut --}}
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">NGN Club member?</p>
            <a href="{{ route('ngnclub.login') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-amber-400 text-amber-600 dark:text-amber-400 text-sm font-semibold hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                ★ NGN Club Login
            </a>
        </div>

        <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
            <a href="/ngn-admin/login" class="hover:text-gray-600 dark:hover:text-gray-300 transition">Staff login</a>
        </p>

    </div>
</div>
