<div>
    <x-flux-admin::data-table title="Club spending payments" description="Individual payment lines applied to member debts (FIFO).">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.club-spending-payments.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">Record payment</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search POS invoice or member…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>POS invoice</flux:table.column>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Received</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="csp-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pos_invoice ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->clubMember?->full_name ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->clubMember?->phone ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch_id ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">£{{ number_format((float) $r->received_total, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate" title="{{ $r->note }}">{{ $r->note ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.club-spending-payments.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $r->id }})" wire:confirm="Delete this payment and revert FIFO allocations?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
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
