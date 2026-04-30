<div>
    <x-flux-admin::data-table title="Judopay subscriptions" description="Recurring card-on-file billing subscriptions.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/judopay-subscription/create')" class="!rounded-none">New subscription</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search consumer ref, card, receipt or auth code…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="paused">Paused</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.billing_frequency" placeholder="Frequency">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="weekly">Weekly</flux:select.option>
                        <flux:select.option value="monthly">Monthly</flux:select.option>
                        <flux:select.option value="annually">Annually</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>Consumer</flux:table.column>
                <flux:table.column>Card</flux:table.column>
                <flux:table.column>Frequency</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Start / End</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="js-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->consumer_reference }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">•••• {{ $r->card_last_four }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->billing_frequency }} · day {{ $r->billing_day }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->amount, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '—' }}
                            @if($r->end_date) → {{ \Carbon\Carbon::parse($r->end_date)->format('d M Y') }} @endif
                        </flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/judopay-subscription/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
