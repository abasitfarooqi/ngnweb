<div>
    <x-flux-admin::data-table title="Club spending payments" description="Individual payment lines applied to member debts (FIFO).">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/club-member-spending-payment/create')" class="!rounded-none">Record payment</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search POS invoice or member…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>POS invoice</flux:table.column>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Received</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="csp-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pos_invoice }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->clubMember?->customer ? $r->clubMember->customer->first_name.' '.$r->clubMember->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch_id ? \App\Models\Branch::find($r->branch_id)?->name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">£{{ number_format((float) $r->received_total, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $r->note }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/club-member-spending-payment/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
