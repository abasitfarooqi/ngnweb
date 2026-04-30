<div>
    <x-flux-admin::data-table title="Purchase requests" description="Parts required for incoming motorbike stock.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/purchase-request/create')" class="!rounded-none">New request</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search note…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_posted" placeholder="Posted">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Posted</flux:select.option>
                        <flux:select.option value="0">Draft</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>Request #</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="pr-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">#{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->items_count }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-md truncate">{{ $r->note }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_posted" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/purchase-request/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
