<div class="space-y-6">
    <div>
        <flux:heading size="xl">Refer a friend (rental)</flux:heading>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Genuine rental customers can refer someone who has not rented with us before.</p>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(! $eligible)
        <flux:card class="p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">You can refer a friend after you have paid one weekly rental invoice.</p>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Available points</p>
                <p class="mt-1 text-2xl font-semibold">{{ $availablePoints }}</p>
            </flux:card>
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Pending points</p>
                <p class="mt-1 text-2xl font-semibold">{{ $pendingPoints }}</p>
            </flux:card>
            <flux:card class="p-5">
                <p class="text-xs uppercase tracking-wide text-gray-500">Reward</p>
                <p class="mt-1 text-sm">{{ $pointsPerWeek }} points = 1 free week at your weekly rent when applied.</p>
            </flux:card>
        </div>

        @if($shareCode)
            <flux:card class="p-5">
                <p class="text-sm font-medium">Your latest code</p>
                <p class="mt-1 font-mono text-lg">{{ $shareCode }}</p>
                <p class="mt-1 text-sm break-all">{{ $shareUrl }}</p>
            </flux:card>
        @endif

        <flux:card class="p-5 space-y-3">
            <p class="text-sm font-medium">Refer someone</p>
            <flux:input wire:model="name" placeholder="Their name" class="!rounded-none" />
            @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <flux:input wire:model="phone" placeholder="UK mobile starting 07" class="!rounded-none" />
            @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <flux:input wire:model="email" placeholder="Email (optional)" class="!rounded-none" />
            @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <flux:button variant="primary" wire:click="submit" class="!rounded-none bg-brand-red text-white">Send referral</flux:button>
        </flux:card>

        <div class="space-y-3">
            <h2 class="text-sm font-semibold">Your referrals</h2>
            @forelse($rows as $row)
                <flux:card class="p-4">
                    <div class="flex justify-between gap-3 flex-wrap">
                        <div>
                            <p class="font-medium">{{ $row->submitted_name }}</p>
                            <p class="text-xs text-gray-500">{{ $row->created_at?->format('d M Y') }} · {{ $row->referral_code }}</p>
                        </div>
                        <span class="text-sm">{{ $row->friendlyStatus() }}</span>
                    </div>
                </flux:card>
            @empty
                <p class="text-sm text-gray-500">You have not referred anyone yet.</p>
            @endforelse
        </div>
    @endif
</div>
