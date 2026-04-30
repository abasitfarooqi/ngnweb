<div>
    <x-flux-admin::data-table title="Products" description="Inventory catalogue across all branches.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/ngn-product/create')" class="!rounded-none">New product</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, SKU or EAN…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.brand_id" placeholder="Brand">
                        <flux:select.option value="">All brands</flux:select.option>
                        @foreach($brands as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.category_id" placeholder="Category">
                        <flux:select.option value="">All categories</flux:select.option>
                        @foreach($categories as $c)
                            <flux:select.option value="{{ $c->id }}">{{ $c->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.is_ecommerce" placeholder="Shop">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">On shop</flux:select.option>
                        <flux:select.option value="0">Internal</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.dead" placeholder="Active">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="0">Active</flux:select.option>
                        <flux:select.option value="1">Discontinued</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>SKU</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Brand</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Stock</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="pr-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->sku }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-xs truncate">{{ $r->name }} <span class="text-xs text-zinc-500">{{ $r->variation }}</span></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->brand?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->category?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->pos_price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->global_stock }}</flux:table.cell>
                        <flux:table.cell>
                            @if($r->dead)
                                <x-flux-admin::status-badge status="discontinued" />
                            @else
                                <x-flux-admin::status-badge status="active" />
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/ngn-product/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No products.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
