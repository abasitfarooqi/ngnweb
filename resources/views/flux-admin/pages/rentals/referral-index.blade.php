<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Rental referrals</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Investigate referrals, pending points and free-week rewards. Separate from Club.</p>
        </div>
        <a href="{{ route('flux-admin.rental-operations.index') }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Rentals home</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-flux-admin::stat-card label="Under review" :value="number_format($metrics['review'])" icon="clipboard-document-check" colour="amber" />
        <x-flux-admin::stat-card label="Pending points" :value="number_format($metrics['pending_points'])" icon="clock" colour="blue" />
        <x-flux-admin::stat-card label="Available points" :value="number_format($metrics['available_points'])" icon="check-circle" colour="green" />
        <x-flux-admin::stat-card label="Redeemed value" :value="'£'.number_format($metrics['redeemed_value'], 2)" icon="currency-pound" colour="purple" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-flux-admin::stat-card label="Submitted" :value="number_format($metrics['submitted'])" icon="paper-airplane" colour="zinc" />
        <x-flux-admin::stat-card label="Warnings" :value="number_format($metrics['warnings'])" icon="exclamation-triangle" colour="red" />
        <x-flux-admin::stat-card label="Early releases" :value="number_format($metrics['early_releases'])" icon="bolt" colour="indigo" />
    </div>

    @if($canCreate)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Add referral (staff)</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">The referrer must already have one paid weekly invoice. Referral must be recorded before the friend’s hire starts.</p>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <flux:input wire:model.live.debounce.300ms="newReferrerSearch" placeholder="Search referrer…" variant="filled" class="!rounded-none" />
                    @if($newReferrerId)
                        <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Selected customer #{{ $newReferrerId }}</p>
                    @endif
                    @if($referrerChoices)
                        <div class="mt-1 border border-zinc-200 dark:border-zinc-700 max-h-40 overflow-y-auto">
                            @foreach($referrerChoices as $choice)
                                <button type="button" wire:click="$set('newReferrerId', {{ $choice->id }})" class="block w-full text-left px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    {{ $choice->first_name }} {{ $choice->last_name }} · {{ $choice->phone }} · #{{ $choice->id }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @error('newReferrerId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <flux:input wire:model="newName" placeholder="Friend’s name" variant="filled" class="!rounded-none" />
                <flux:input wire:model="newPhone" placeholder="07 mobile" variant="filled" class="!rounded-none" />
                <flux:input wire:model="newEmail" placeholder="Email (optional)" variant="filled" class="!rounded-none" />
            </div>
            @error('newName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @error('newPhone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <div class="mt-3">
                <flux:button size="sm" variant="primary" wire:click="createReferral" class="!rounded-none">Save referral</flux:button>
            </div>
        </div>
    @endif

    <div class="flux-admin-toolbar mb-0 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search code, name, phone, referrer…" variant="filled" class="!rounded-none" />
            </div>
            <div class="min-w-0 w-full sm:w-48">
                <flux:select wire:model.live="status" class="!rounded-none">
                    <flux:select.option value="">All statuses</flux:select.option>
                    <flux:select.option value="submitted">Submitted</flux:select.option>
                    <flux:select.option value="matched">Matched</flux:select.option>
                    <flux:select.option value="qualifying">Qualifying</flux:select.option>
                    <flux:select.option value="review">Review</flux:select.option>
                    <flux:select.option value="approved">Approved</flux:select.option>
                    <flux:select.option value="rejected">Rejected</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Referrer</flux:table.column>
                    <flux:table.column>Referred</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Points</flux:table.column>
                    <flux:table.column>Available from</flux:table.column>
                    <flux:table.column>Source</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($rows as $row)
                        @php $credit = $row->credit(); @endphp
                        <flux:table.row wire:key="rr-{{ $row->id }}">
                            <flux:table.cell class="whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($row->referrer)
                                    <a href="{{ route('flux-admin.customers.show', $row->referrer_customer_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $row->referrer->first_name }} {{ $row->referrer->last_name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div>{{ $row->submitted_name }}</div>
                                <div class="text-xs text-zinc-500">{{ $row->submitted_phone }}@if($row->referred) · matched #{{ $row->referred_customer_id }}@endif</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-sm">{{ $row->status }}</span>
                                @if($row->hasWarning())
                                    <span class="ml-1 text-xs text-amber-700 dark:text-amber-400">warning</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-sm">{{ $credit?->status ?? '—' }}{{ $credit ? ' ('.$credit->points.')' : '' }}</flux:table.cell>
                            <flux:table.cell class="text-sm whitespace-nowrap">{{ $credit?->available_from?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell class="text-sm">{{ $row->source }}</flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Investigate</a>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-sm text-zinc-500">No referrals yet.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800">
            {{ $rows->links() }}
        </div>
    </div>
</div>
