<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <a href="/" class="flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="NGN Motor">
            </a>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900 dark:text-white">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Or
                <a href="{{ route('register') }}" class="font-medium text-brand-red hover:text-red-700">
                    create a new account
                </a>
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 py-6">
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email address
                    </label>
                    <input
                        id="email"
                        type="email"
                        wire:model.defer="email"
                        autocomplete="username"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"
                    >
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        wire:model.defer="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"
                    >
                    @error('password')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                        <input
                            type="checkbox"
                            wire:model="remember"
                            class="h-4 w-4 text-brand-red focus:ring-brand-red border-gray-300"
                        >
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-brand-red hover:text-red-700">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <input
                    type="hidden"
                    x-data
                    x-init="$wire.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone"
                >

                <button
                    type="submit"
                    class="w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-brand-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
