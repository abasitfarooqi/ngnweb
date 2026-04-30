<div>
    <x-flux-admin::data-table title="Club spending" description="Member account spending (FIFO-tracked debt).">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/club-member-spending/create')" class="!rounded-none">Log spend</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search POS invoice or member…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_paid" placeholder="Paid">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Settled</flux:select.option>
                        <flux:select.option value="0">Outstanding</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>POS invoice</flux:table.column>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column>Outstanding</flux:table.column>
                <flux:table.column>Settled</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cs-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pos_invoice }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->clubMember?->customer ? $r->clubMember->customer->first_name.' '.$r->clubMember->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch_id ? \App\Models\Branch::find($r->branch_id)?->name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->total, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">£{{ number_format((float) $r->paid_amount, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-amber-600 dark:text-amber-400">£{{ number_format((float) ($r->total - $r->paid_amount), 2) }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_paid" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/club-member-spending/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
