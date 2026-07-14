<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Motorbike pricing</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Set weekly rent and deposit. Updates version current rows (old stays as history).</p>
        </div>
        <a href="{{ route('flux-admin.renting-pricing.index') }}">
            <flux:button size="sm" variant="ghost" class="!rounded-none">Pricing history CRUD</flux:button>
        </a>
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
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Not priced ({{ $unpriced->count() }})</h2>
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
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
                            <tr><td colspan="3" class="px-3 py-6 text-center text-zinc-500">All motorbikes have pricing.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Current pricing ({{ $current->count() }})</h2>
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
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
                            <tr><td colspan="4" class="px-3 py-6 text-center text-zinc-500">No current pricing rows.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
