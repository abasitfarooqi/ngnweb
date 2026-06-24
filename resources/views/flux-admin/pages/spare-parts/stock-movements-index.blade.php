<div>
    <x-flux-admin::data-table title="Spare parts · Stock movements" description="In/out ledger for spare-part inventory.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.sp-stock-movements.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New movement</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search ref doc or remarks…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.transaction_type" placeholder="Type">
                        <flux:select.option value="">Any type</flux:select.option>
                        <flux:select.option value="purchase">Purchase</flux:select.option>
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="transfer">Transfer</flux:select.option>
                        <flux:select.option value="adjustment">Adjustment</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'transaction_date'" :direction="$sortField === 'transaction_date' ? $sortDirection : null" wire:click="sortBy('transaction_date')">Date</flux:table.column>
                <flux:table.column>Part</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>In</flux:table.column>
                <flux:table.column>Out</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Ref</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="spsm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->part?->part_number }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->part?->name }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $branches->firstWhere('id', $r->branch_id)?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">{{ $r->in ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-red-600 dark:text-red-400">{{ $r->out ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->transaction_type }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->ref_doc_no }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.sp-stock-movements.edit', $r) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $r->id }})" wire:confirm="Delete this movement?" icon="trash" class="!rounded-none">Delete</flux:button>
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
