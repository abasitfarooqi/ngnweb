<div>
{{-- Hero bar --}}
<div class="bg-gray-900 text-white py-8 px-4">
    <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-amber-400 text-sm font-semibold tracking-widest uppercase mb-1">★ NGN Club Member</p>
            <h1 class="text-2xl sm:text-3xl font-bold">Welcome back, {{ $member->full_name }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $member->email }} · {{ $member->phone }}</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            @if($qualifiedReferral)
                <a href="{{ route('ngnclub.referral', $member->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Refer a Friend
                </a>
            @endif
            <button wire:click="logout" type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-600 text-gray-300 text-sm hover:border-gray-400 hover:text-white transition">
                Logout
            </button>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-8">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <flux:card class="p-5 text-center">
            <p class="text-3xl font-black text-amber-500">{{ $purchases->count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium uppercase tracking-wide">Purchases</p>
        </flux:card>
        <flux:card class="p-5 text-center">
            <p class="text-3xl font-black text-gray-900 dark:text-white">£{{ number_format($totalSpend, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium uppercase tracking-wide">Total Spent</p>
        </flux:card>
        <flux:card class="p-5 text-center">
            <p class="text-3xl font-black text-green-600">£{{ number_format($totalDiscount, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium uppercase tracking-wide">Earned Rewards</p>
        </flux:card>
        <flux:card class="p-5 text-center">
            <p class="text-3xl font-black text-brand-red">£{{ number_format($availableCredit, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium uppercase tracking-wide">Available Credit</p>
        </flux:card>
    </div>

    {{-- Bike info --}}
    @if($member->vrm)
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">My Bike</h2>
            <div class="flex flex-wrap gap-6 items-center">
                <div class="bg-yellow-400 border-2 border-black px-4 py-2 font-black text-lg tracking-widest uppercase shadow-sm">
                    {{ $member->vrm }}
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    @if($member->make || $member->model)
                        <p class="font-semibold text-base">{{ $member->make }} {{ $member->model }}</p>
                    @endif
                    @if($member->year)
                        <p class="text-gray-500">{{ $member->year }}</p>
                    @endif
                </div>
            </div>
        </flux:card>
    @endif

    {{-- Purchases history --}}
    <flux:card class="p-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Purchase History</h2>
        @if($purchases->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="text-sm">No purchases recorded yet. Visit any NGN branch and mention your club membership!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                            <th class="text-right py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Total</th>
                            <th class="text-right py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Reward</th>
                            <th class="text-right py-2 font-semibold text-gray-600 dark:text-gray-400">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($purchases as $purchase)
                            <tr>
                                <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">
                                    {{ $purchase->date ? \Carbon\Carbon::parse($purchase->date)->format('d M Y') : '—' }}
                                </td>
                                <td class="py-2 pr-4 text-right font-medium text-gray-900 dark:text-white">
                                    £{{ number_format($purchase->total, 2) }}
                                </td>
                                <td class="py-2 pr-4 text-right text-green-600 font-medium">
                                    £{{ number_format($purchase->discount, 2) }}
                                </td>
                                <td class="py-2 text-right text-gray-500">
                                    {{ $purchase->pos_invoice ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>

    {{-- Redemption history --}}
    @if($redemptions->isNotEmpty())
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Reward Redemptions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                            <th class="text-right py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Redeemed</th>
                            <th class="text-left py-2 font-semibold text-gray-600 dark:text-gray-400">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($redemptions as $redeem)
                            <tr>
                                <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">
                                    {{ $redeem->date ? \Carbon\Carbon::parse($redeem->date)->format('d M Y') : '—' }}
                                </td>
                                <td class="py-2 pr-4 text-right text-brand-red font-medium">
                                    £{{ number_format($redeem->redeem_total, 2) }}
                                </td>
                                <td class="py-2 text-gray-500">{{ $redeem->note ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif

    {{-- Referral CTA --}}
    @if($qualifiedReferral)
        <flux:card class="p-6 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Refer a friend & earn rewards</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        You've qualified for referral rewards! Share your referral link and both you and your friend get rewarded.
                    </p>
                </div>
                <a href="{{ route('ngnclub.referral', $member->id) }}"
                   class="flex-shrink-0 px-5 py-2.5 bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                    Get Referral Link
                </a>
            </div>
        </flux:card>
    @endif

</div>
</div>
