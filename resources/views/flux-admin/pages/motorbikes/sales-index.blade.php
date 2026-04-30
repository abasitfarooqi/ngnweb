<div>
    <x-flux-admin::data-table
        title="Motorbike sales"
        description="Used motorbikes listed for sale and sold bikes."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/motorbikes-sale/create')" class="!rounded-none">New sale</flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search registration, model or buyer…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_sold" placeholder="Availability">
                        <flux:select.option value="">All</flux:select.option>
                        <flux:select.option value="0">Available</flux:select.option>
                        <flux:select.option value="1">Sold</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'price'" :direction="$sortField === 'price' ? $sortDirection : null" wire:click="sortBy('price')">Price</flux:table.column>
                <flux:table.column>Mileage</flux:table.column>
                <flux:table.column>Purchased</flux:table.column>
                <flux:table.column>Sold</flux:table.column>
                <flux:table.column>Buyer</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($sales as $sale)
                    <flux:table.row wire:key="sale-{{ $sale->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $sale->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $sale->motorbike?->make }} {{ $sale->motorbike?->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $sale->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $sale->mileage }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $sale->date_of_purchase ? \Carbon\Carbon::parse($sale->date_of_purchase)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$sale->is_sold ? 'yes' : 'no'" :map="['yes' => ['red', 'Sold'], 'no' => ['green', 'Available']]" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $sale->buyer_name ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/motorbikes-sale/'.$sale->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No sales records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $sales->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
