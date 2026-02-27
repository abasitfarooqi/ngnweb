<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">NGN Club</h1>

    @if ($clubMember)
        {{-- Active Membership --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Membership Status</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $clubMember->is_active ? 'Active Member' : 'Inactive Member' }}
                        @if ($clubMember->is_partner)
                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                Partner
                            </span>
                        @endif
                    </p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $clubMember->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                    {{ $clubMember->is_active ? '✓ Active' : 'Inactive' }}
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Purchases</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        £{{ number_format($clubMember->totalPurchases ?? 0, 2) }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Credits</p>
                    <p class="mt-1 text-2xl font-semibold text-brand-red">
                        £{{ number_format($clubMember->availableRedeemableBalance ?? 0, 2) }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Redeemed</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        £{{ number_format($clubMember->totalRedeemed ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Benefits --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Your Benefits</h2>
            
            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $clubMember->is_partner ? '17.5% credit on repairs, maintenance, accessories and MOT' : '10% credit on repairs, maintenance, accessories and MOT' }}
                    </p>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $clubMember->is_partner ? 'Up to 4% credit on motorcycle purchases' : '2% credit on motorcycle purchases' }}
                    </p>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                        Credits available after 48 hours
                    </p>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                        Credits expire after 6 months
                    </p>
                </div>
            </div>
        </div>

        {{-- Terms Link --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                View full <a href="/ngn-club" target="_blank" class="font-medium underline hover:no-underline">NGN Club Terms & Conditions</a>
            </p>
        </div>
    @else
        {{-- Not a Member --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-brand-red bg-opacity-10 mb-4">
                <svg class="h-8 w-8 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Join NGN Club</h2>
            
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Earn 10% credit on repairs and services, 2% on motorcycle purchases, and enjoy exclusive benefits.
            </p>

            <a href="/ngn-club/subscribe" target="_blank"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                Join NGN Club
            </a>
        </div>
    @endif
</div>
