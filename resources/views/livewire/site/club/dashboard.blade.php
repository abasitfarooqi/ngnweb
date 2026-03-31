<div>
    <div class="bg-gray-900 text-white py-8 px-4">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="text-amber-400 text-sm font-semibold tracking-widest uppercase mb-1">★ NGN Club member</p>
                <h1 class="text-2xl sm:text-3xl font-bold">Welcome back, {{ $member->full_name }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ $member->email }} · {{ $member->phone }}</p>
            </div>
            <div class="flex gap-3 flex-wrap items-center">
                @if ($dash['qualified_referal'])
                    <a href="{{ route('ngnclub.referral', $member->id) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 border border-amber-400">
                        Refer a friend
                    </a>
                @endif
                <button wire:click="logout" type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-600 text-gray-300 text-sm hover:border-gray-400 hover:text-white">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <x-club.dashboard-content :member="$member" :dash="$dash" sell-form-id="clubdash_sell_site" />
    </div>
</div>
