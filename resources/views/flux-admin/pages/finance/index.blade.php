<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Finance Applications</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage all finance applications and contracts.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by ID or customer name…" icon="magnifying-glass" size="sm" />
            </div>
            <div class="w-full sm:w-48">
                <flux:select wire:model.live="contractType" size="sm" placeholder="All contract types">
                    <flux:select.option value="">All Types</flux:select.option>
                    <flux:select.option value="is_used">Used</flux:select.option>
                    <flux:select.option value="is_new_latest">New Latest</flux:select.option>
                    <flux:select.option value="is_used_latest">Used Latest</flux:select.option>
                    <flux:select.option value="is_used_extended">Used Extended</flux:select.option>
                    <flux:select.option value="is_used_extended_custom">Used Extended Custom</flux:select.option>
                    <flux:select.option value="is_subscription">Subscription</flux:select.option>
                </flux:select>
            </div>
            <div class="w-full sm:w-40">
                <flux:select wire:model.live="status" size="sm" placeholder="All statuses">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
            </div>
            <div class="w-full sm:w-28">
                <flux:select wire:model.live="perPage" size="sm">
                    <flux:select.option value="20">20</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'id'" :direction="$sortDirection" wire:click="sortBy('id')">ID</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Contract Type</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'deposit'" :direction="$sortDirection" wire:click="sortBy('deposit')">Deposit</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'weekly_instalment'" :direction="$sortDirection" wire:click="sortBy('weekly_instalment')">Weekly Instalment</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'contract_date'" :direction="$sortDirection" wire:click="sortBy('contract_date')">Contract Date</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($applications as $app)
                    <flux:table.row wire:key="fa-{{ $app->id }}" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50" wire:click="$dispatch('navigate', { url: '{{ route('flux-admin.finance.show', $app) }}' })" onclick="window.location='{{ route('flux-admin.finance.show', $app) }}'">
                        <flux:table.cell class="font-mono text-xs">{{ $app->id }}</flux:table.cell>
                        <flux:table.cell>
                            @if($app->customer)
                                {{ $app->customer->first_name }} {{ $app->customer->last_name }}
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $type = match(true) {
                                    (bool) $app->is_subscription => 'Subscription',
                                    (bool) $app->is_new_latest => 'New Latest',
                                    (bool) $app->is_used_latest => 'Used Latest',
                                    (bool) $app->is_used_extended_custom => 'Used Ext. Custom',
                                    (bool) $app->is_used_extended => 'Used Extended',
                                    (bool) $app->is_used => 'Used',
                                    default => 'Unknown',
                                };
                            @endphp
                            <flux:badge color="zinc" size="sm">{{ $type }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>£{{ number_format($app->deposit ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($app->weekly_instalment ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $app->contract_date ? \Carbon\Carbon::parse($app->contract_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $app->items_count }}</flux:table.cell>
                        <flux:table.cell>
                            @if($app->is_cancelled)
                                <flux:badge color="red" size="sm">Cancelled</flux:badge>
                            @else
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No finance applications found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>
