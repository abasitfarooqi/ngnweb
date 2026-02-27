<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold mb-1">Welcome back, {{ $member->name }}!</h1>
            <p class="text-red-100">Member since {{ \Carbon\Carbon::parse($member->member_since)->format('F Y') }}</p>
        </div>
        <flux:badge color="yellow" size="lg">NGN Club Member</flux:badge>
    </div>
</div>

{{-- Dashboard --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach([
            ['label' => 'Your Discount',  'value' => $member->discount_rate,                        'color' => 'text-brand-red'],
            ['label' => 'Total Spent',    'value' => '£' . number_format($member->total_spent, 2),  'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Reward Points',  'value' => $member->points,                                'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Referrals',      'value' => $member->referrals_count,                      'color' => 'text-gray-900 dark:text-white'],
        ] as $stat)
            <flux:card class="p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ $stat['label'] }}</p>
                <p class="text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </flux:card>
        @endforeach
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Recent Activity --}}
            <flux:card class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Recent Activity</h2>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach([
                        ['service' => 'Full Service – Honda PCX 125', 'date' => '15 Jan 2026', 'amount' => '£135.00', 'saved' => 'Saved £15.00'],
                        ['service' => 'MOT Test',                     'date' => '28 Dec 2025', 'amount' => '£27.00',  'saved' => 'Saved £3.00'],
                        ['service' => 'Chain & Sprocket Kit',         'date' => '10 Nov 2025', 'amount' => '£108.00', 'saved' => 'Saved £12.00'],
                    ] as $activity)
                        <div class="flex items-center justify-between py-4">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $activity['service'] }}</p>
                                <p class="text-sm text-gray-500">{{ $activity['date'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $activity['amount'] }}</p>
                                <p class="text-xs text-green-600">{{ $activity['saved'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:card>

            {{-- Rewards --}}
            <flux:card class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Your Rewards</h2>
                <div class="bg-gray-50 dark:bg-gray-900 p-6 mb-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 mb-1">Available Points</p>
                    <p class="text-4xl font-bold text-brand-red mb-3">{{ $member->points }} Points</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 mb-2">
                        <div class="bg-brand-red h-2 transition-all" style="width: {{ min(($member->points / 200) * 100, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">{{ max(0, 200 - $member->points) }} points to next reward</p>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Earn 1 point for every £10 spent. Redeem 200 points for a £20 voucher!</p>
            </flux:card>
        </div>

        {{-- Right Column --}}
        <div class="space-y-5">

            {{-- Profile --}}
            <flux:card class="p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Profile</h3>
                <div class="space-y-3 text-sm mb-4">
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $member->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phone</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $member->phone }}</p>
                    </div>
                </div>
                <flux:button variant="outline" size="sm" class="w-full">Edit Profile</flux:button>
            </flux:card>

            {{-- Referral --}}
            <flux:card class="p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Refer a Friend</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Share your code and earn £20 credit when they join!</p>
                <div class="bg-gray-50 dark:bg-gray-900 p-3 mb-3 text-center border border-gray-200 dark:border-gray-700">
                    <p class="text-2xl font-bold text-brand-red tracking-widest">{{ $member->referral_code }}</p>
                </div>
                <flux:button variant="filled" size="sm" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">Share Code</flux:button>
            </flux:card>

            {{-- Quick Actions --}}
            <flux:card class="p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <flux:button href="/contact/service-booking" variant="outline" size="sm" class="w-full">Book Service</flux:button>
                    <flux:button href="/mot/book" variant="outline" size="sm" class="w-full">Book MOT</flux:button>
                    <flux:button href="/contact" variant="outline" size="sm" class="w-full">Contact Us</flux:button>
                </div>
            </flux:card>
        </div>
    </div>
</div>
</div>
