<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Price Adjustment</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Rental pricing history first, then motorbike weekly rent and deposit controls.</p>
        </div>
        <a href="{{ route('flux-admin.renting-pricing.create') }}">
            <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New price</flux:button>
        </a>
    </div>

    <div class="mb-6 border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input
            wire:model.live.debounce.300ms="regSearch"
            placeholder="Search by registration, make or model…"
            icon="magnifying-glass"
            class="max-w-md"
        />
        @if(trim($regSearch) !== '')
            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Filtering both lists. Clear the search to see all bikes.</p>
        @endif
    </div>

    <div class="mb-6 border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-2 border-b border-zinc-200 px-4 py-3 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-900 dark:text-white">Rental pricing history</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Latest 50 price rows. Use search above to narrow by registration, make or model.</p>
            </div>
            <a href="{{ route('flux-admin.renting-pricing.create') }}">
                <flux:button size="xs" variant="ghost" icon="plus" class="!rounded-none">Add history row</flux:button>
            </a>
        </div>
        <div class="touch-pan-x overflow-x-auto">
            <table class="w-full min-w-[52rem] text-left text-sm">
                <thead class="bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                    <tr>
                        <th class="px-3 py-2">Vehicle</th>
                        <th class="px-3 py-2">Weekly price</th>
                        <th class="px-3 py-2">Minimum deposit</th>
                        <th class="px-3 py-2">Current</th>
                        <th class="px-3 py-2">Effective</th>
                        <th class="px-3 py-2">Updated by</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pricingHistory as $r)
                        <tr class="border-t border-zinc-100 dark:border-zinc-800" wire:key="pricing-history-{{ $r->id }}">
                            <td class="px-3 py-2">
                                <div class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no ?: '—' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ trim(($r->motorbike?->make ?? '').' '.($r->motorbike?->model ?? '')) ?: '—' }}</div>
                            </td>
                            <td class="px-3 py-2 text-zinc-900 dark:text-white">£{{ number_format((float) $r->weekly_price, 2) }}</td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">£{{ number_format((float) $r->minimum_deposit, 2) }}</td>
                            <td class="px-3 py-2"><x-flux-admin::status-badge :status="(bool) $r->iscurrent" /></td>
                            <td class="px-3 py-2 whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $r->update_date ? \Carbon\Carbon::parse($r->update_date)->format('d M Y') : '—' }}</td>
                            <td class="px-3 py-2 text-zinc-600 dark:text-zinc-400">{{ trim(($r->user?->first_name ?? '').' '.($r->user?->last_name ?? '')) ?: '—' }}</td>
                            <td class="px-3 py-2">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('flux-admin.renting-pricing.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                    <flux:button size="xs" variant="ghost" wire:click="deleteHistoryPricing({{ $r->id }})" wire:confirm="Delete this pricing entry?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-zinc-500">No pricing history matches this search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($selectedMotorbikeId)
        <div class="mb-6 border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">
                {{ $editingPricingId ? 'Update pricing' : 'Add pricing' }} — {{ $selectedReg }}
            </h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <x-flux-admin::field-group label="Weekly price (£)" required :error="$errors->first('weeklyPrice')">
                    <flux:input type="number" step="0.01" min="0" wire:model="weeklyPrice" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Minimum deposit (£)" required :error="$errors->first('minimumDeposit')">
                    <flux:input type="number" step="0.01" min="0" wire:model="minimumDeposit" />
                </x-flux-admin::field-group>
                <div class="flex items-end gap-2">
                    <flux:button wire:click="savePricing" variant="primary" class="!rounded-none">Save</flux:button>
                    <flux:button wire:click="cancelEdit" variant="ghost" class="!rounded-none">Cancel</flux:button>
                </div>
            </div>
            @if($editingPricingId)
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Set both prices to 0 to delete this pricing row.</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">
                    Not priced ({{ number_format($unpricedTotal) }})
                </h2>
                @if($unpriced->isNotEmpty())
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        Showing {{ number_format($unpriced->count()) }} of {{ number_format($unpricedTotal) }}
                    </p>
                @endif
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                        <tr>
                            <th class="px-3 py-2">Reg</th>
                            <th class="px-3 py-2">Make / model</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unpriced as $bike)
                            <tr class="border-t border-zinc-100 dark:border-zinc-800" wire:key="unpriced-{{ $bike->id }}">
                                <td class="px-3 py-2 font-mono text-xs">{{ $bike->reg_no ?: '—' }}</td>
                                <td class="px-3 py-2">{{ $bike->make }} {{ $bike->model }}</td>
                                <td class="px-3 py-2 text-right">
                                    <flux:button size="xs" variant="ghost" wire:click="selectUnpriced({{ $bike->id }})" class="!rounded-none">Price</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-zinc-500">No motorbikes match this search.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($hasMoreUnpriced)
                    <div
                        x-data
                        x-intersect:enter.margin.120px="$wire.loadMoreUnpriced()"
                        class="border-t border-zinc-100 py-3 text-center text-xs text-zinc-400 dark:border-zinc-800"
                        wire:loading.class="opacity-60"
                        wire:target="loadMoreUnpriced,regSearch"
                    >
                        <span wire:loading wire:target="loadMoreUnpriced">Loading more…</span>
                        <span wire:loading.remove wire:target="loadMoreUnpriced">Scroll for more</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">
                    Current pricing ({{ number_format($currentTotal) }})
                </h2>
                @if($current->isNotEmpty())
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        Showing {{ number_format($current->count()) }} of {{ number_format($currentTotal) }}
                    </p>
                @endif
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                        <tr>
                            <th class="px-3 py-2">Reg</th>
                            <th class="px-3 py-2">Weekly</th>
                            <th class="px-3 py-2">Deposit</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($current as $row)
                            <tr class="border-t border-zinc-100 dark:border-zinc-800" wire:key="priced-{{ $row->id }}">
                                <td class="px-3 py-2 font-mono text-xs">{{ $row->motorbike?->reg_no ?: '—' }}</td>
                                <td class="px-3 py-2">£{{ number_format((float) $row->weekly_price, 2) }}</td>
                                <td class="px-3 py-2">£{{ number_format((float) $row->minimum_deposit, 2) }}</td>
                                <td class="px-3 py-2 text-right">
                                    <flux:button size="xs" variant="ghost" wire:click="editCurrent({{ $row->id }})" class="!rounded-none">Edit</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-6 text-center text-zinc-500">No pricing rows match this search.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($hasMoreCurrent)
                    <div
                        x-data
                        x-intersect:enter.margin.120px="$wire.loadMoreCurrent()"
                        class="border-t border-zinc-100 py-3 text-center text-xs text-zinc-400 dark:border-zinc-800"
                        wire:loading.class="opacity-60"
                        wire:target="loadMoreCurrent,regSearch"
                    >
                        <span wire:loading wire:target="loadMoreCurrent">Loading more…</span>
                        <span wire:loading.remove wire:target="loadMoreCurrent">Scroll for more</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
