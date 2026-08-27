<div class="flex min-h-full flex-col justify-center px-4 py-10 sm:px-6">
    <div class="mx-auto w-full max-w-md">
        <div class="mb-8 text-center">
            <img
                src="{{ asset(config('site.logo', 'img/ngn-motor-logo-fit-small-ngn.png')) }}"
                alt="NGN Motors"
                class="mx-auto h-12 w-auto"
                width="180"
                height="48"
                fetchpriority="high"
            >
            <h1 class="mt-5 text-2xl font-bold tracking-tight text-gray-900">Flux Admin</h1>
            <p class="mt-2 text-sm text-gray-600">Staff sign in to continue</p>
        </div>

        <div class="border border-gray-200 bg-white px-6 py-8 shadow-sm sm:px-8">
            <form wire:submit="login" class="space-y-5">
                <div>
                    <label for="flux-admin-email" class="ngn-label">Email</label>
                    <input
                        id="flux-admin-email"
                        type="email"
                        wire:model="email"
                        autocomplete="username"
                        autofocus
                        placeholder="you@ngnmotors.co.uk"
                        class="ngn-input @error('email') !border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="flux-admin-password" class="ngn-label">Password</label>
                    <input
                        id="flux-admin-password"
                        type="password"
                        wire:model="password"
                        autocomplete="current-password"
                        class="ngn-input @error('password') !border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="remember" class="accent-brand-green">
                    <span>Remember me</span>
                </label>

                <button
                    type="submit"
                    class="ngn-btn-primary w-full py-2.5"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="login">Sign in</span>
                    <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Signing in…
                    </span>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            Legacy Backpack admin:
            <a href="/ngn-admin/login" class="font-medium text-gray-700 underline underline-offset-2 hover:text-gray-900">ngn-admin login</a>
        </p>
    </div>
</div>
