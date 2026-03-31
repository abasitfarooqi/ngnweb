<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">NGN Club</h1>

    @if ($clubMember && $dash)
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('account.dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-red underline">← Back to account</a>
            @if ($dash['qualified_referal'])
                <a href="{{ route('ngnclub.referral', $clubMember->id) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold bg-amber-500 text-white hover:bg-amber-600 border border-amber-400">
                    Refer a friend
                </a>
            @endif
        </div>

        <x-club.dashboard-content :member="$clubMember" :dash="$dash" sell-form-id="clubdash_sell_portal" />
    @else
        <div class="bg-white dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 bg-brand-red bg-opacity-10 mb-4 border border-brand-red/30">
                <svg class="h-8 w-8 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Join NGN Club</h2>

            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Earn 10% credit on repairs and services, 2% on motorcycle purchases, and enjoy exclusive benefits.
            </p>

            <a href="/ngn-club/subscribe" target="_blank" rel="noopener"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium text-white bg-brand-red hover:bg-red-700">
                Join NGN Club
            </a>
        </div>
    @endif
</div>
