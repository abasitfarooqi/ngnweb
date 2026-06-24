<div>
    <x-flux-admin::data-table title="DS order items" description="Pickup and drop-off legs for delivery service orders.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.ds-order-items.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New leg</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, postcode or order…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.ds_order_id" placeholder="Order ID" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Order</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Pickup</flux:table.column>
                <flux:table.column>Drop-off</flux:table.column>
                <flux:table.column>Distance</flux:table.column>
                <flux:table.column>Flags</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="dsi-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->ds_order_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->vrm ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-[16rem] truncate"><span class="font-mono text-xs">{{ $r->pickup_postcode }}</span> · {{ $r->pickup_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-[16rem] truncate"><span class="font-mono text-xs">{{ $r->dropoff_postcode }}</span> · {{ $r->dropoff_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->distance ? number_format((float) $r->distance, 1).' mi' : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-xs">
                            @if($r->moveable)<span class="mr-1 text-emerald-600 dark:text-emerald-400">Moveable</span>@endif
                            @if($r->keys)<span class="mr-1 text-emerald-600 dark:text-emerald-400">Keys</span>@endif
                            @if($r->documents)<span class="text-emerald-600 dark:text-emerald-400">Docs</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.ds-order-items.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this leg?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
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
