<div>
    <x-flux-admin::data-table title="New motorbikes for sale" description="Catalogue of new bikes available for purchase.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/new-motorbikes-for-sale/create')" class="!rounded-none">New listing</flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search make or model…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.type" placeholder="Type">
                        <flux:select.option value="">All types</flux:select.option>
                        <flux:select.option value="Scooter">Scooter</flux:select.option>
                        <flux:select.option value="Standard">Standard</flux:select.option>
                        <flux:select.option value="Super Sport">Super Sport</flux:select.option>
                        <flux:select.option value="Touring">Touring</flux:select.option>
                        <flux:select.option value="Other">Other</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Make</flux:table.column>
                <flux:table.column>Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Sale price</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bikes as $b)
                    <flux:table.row wire:key="for-sale-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $b->make }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->year }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->type }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->engine }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->colour }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $b->sale_new_price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/new-motorbikes-for-sale/'.$b->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $bikes->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
