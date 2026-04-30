<div>
    <x-flux-admin::data-table title="Delivery service orders" description="Customer requests for motorbike collection and delivery.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/ds-order/create')" class="!rounded-none">New order</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer, phone or postcode…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.proceed" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Proceeding</flux:select.option>
                        <flux:select.option value="0">On hold</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'pick_up_datetime'" :direction="$sortField === 'pick_up_datetime' ? $sortDirection : null" wire:click="sortBy('pick_up_datetime')">Pickup</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Address</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Proceed</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="ds-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->pick_up_datetime ? \Carbon\Carbon::parse($r->pick_up_datetime)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->full_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->phone }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-xs truncate">{{ $r->address }} <span class="text-xs text-zinc-400">{{ $r->postcode }}</span></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->ds_order_items_count }}</flux:table.cell>
                        <flux:table.cell><flux:switch :checked="(bool) $r->proceed" wire:click="toggleProceed({{ $r->id }})" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/ds-order/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
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
