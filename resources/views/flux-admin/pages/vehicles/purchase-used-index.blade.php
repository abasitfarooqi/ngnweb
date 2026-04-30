<div>
    <x-flux-admin::data-table title="Used vehicle purchases" description="Log of used motorbikes acquired from private sellers.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/purchase-used-vehicle/create')" class="!rounded-none">New purchase</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search seller, email, reg or phone…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'purchase_date'" :direction="$sortField === 'purchase_date' ? $sortDirection : null" wire:click="sortBy('purchase_date')">Date</flux:table.column>
                <flux:table.column>Seller</flux:table.column>
                <flux:table.column>Vehicle</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Mileage</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Outstanding</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="puv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->purchase_date ? \Carbon\Carbon::parse($r->purchase_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white">{{ $r->full_name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->phone_number }} · {{ $r->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year }} {{ $r->make }} {{ $r->model }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->current_mileage }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-amber-600 dark:text-amber-400">£{{ number_format((float) $r->outstanding, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/purchase-used-vehicle/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
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
