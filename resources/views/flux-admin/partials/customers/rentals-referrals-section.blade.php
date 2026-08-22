<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 mb-4">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Rental referrals</h2>
            <span class="text-sm text-zinc-500">Pending {{ $pendingPoints }} · Available {{ $availablePoints }}</span>
        </div>
        <div class="p-5 space-y-4">
            @if($eligible)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <flux:input wire:model="newName" placeholder="Friend’s name" class="!rounded-none" />
                    <flux:input wire:model="newPhone" placeholder="07 mobile" class="!rounded-none" />
                    <flux:input wire:model="newEmail" placeholder="Email (optional)" class="!rounded-none" />
                    <flux:button size="sm" variant="primary" wire:click="createReferral" class="!rounded-none">Add referral</flux:button>
                </div>
                @error('newName') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            @else
                <p class="text-sm text-zinc-500">This customer is not yet eligible to refer (needs one paid weekly invoice).</p>
            @endif

            <h3 class="text-sm font-semibold">Made</h3>
            <ul class="text-sm space-y-2">
                @forelse($made as $row)
                    <li>
                        <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" class="text-blue-600 dark:text-blue-400 hover:underline">#{{ $row->id }} {{ $row->submitted_name }}</a>
                        · {{ $row->status }}
                        · {{ $row->created_at?->format('d M Y') }}
                    </li>
                @empty
                    <li class="text-zinc-500">None.</li>
                @endforelse
            </ul>

            <h3 class="text-sm font-semibold">Referred by</h3>
            <ul class="text-sm space-y-2">
                @forelse($received as $row)
                    <li>
                        <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" class="text-blue-600 dark:text-blue-400 hover:underline">#{{ $row->id }}</a>
                        · {{ $row->referrer ? $row->referrer->first_name.' '.$row->referrer->last_name : '—' }}
                        · {{ $row->status }}
                    </li>
                @empty
                    <li class="text-zinc-500">None.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
