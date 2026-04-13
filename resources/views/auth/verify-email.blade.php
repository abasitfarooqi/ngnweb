<x-layouts.guest title="Verify Email – NGN Motors">
    <div class="w-full max-w-md mx-auto px-4">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <img class="h-12 w-auto mx-auto" src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors">
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-8 sm:px-8 text-center">

            <div class="mx-auto flex items-center justify-center h-16 w-16 bg-green-50 dark:bg-green-900/20 mb-5">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Verify your email</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Before accessing your account, please verify your email address by clicking the link we sent you. Check your spam folder if you don't see it.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
                    A new verification link has been sent to your email.
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ route('customer.verification.send') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="ngn-btn-primary w-full py-2.5 justify-center">
                        Resend verification email
                    </button>
                </form>

                <form method="POST" action="{{ route('customer.logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="ngn-btn-secondary w-full py-2.5 justify-center">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.guest>
