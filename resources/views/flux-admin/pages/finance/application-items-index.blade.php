<div>
    <x-flux-admin::data-table title="Finance application items" description="Motorbikes allocated to finance applications.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/application-item/create')" class="!rounded-none">New item</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search app, registration or customer…">
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
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>App</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($items as $i)
                    <flux:table.row wire:key="app-item-{{ $i->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $i->id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $i->app_id ?: '#'.$i->application_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $i->application?->customer ? $i->application->customer->first_name.' '.$i->application->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $i->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $i->motorbike ? $i->motorbike->make.' '.$i->motorbike->model : '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $i->is_posted" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/application-item/'.$i->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No items.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $items->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
