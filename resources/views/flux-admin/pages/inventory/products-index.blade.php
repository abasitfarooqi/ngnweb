<div>
    <x-flux-admin::data-table title="Products" description="Inventory catalogue across all branches.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="ghost" icon="arrow-down-tray" wire:click="exportForPos" class="!rounded-none">POS export</flux:button>
            <flux:button size="sm" variant="ghost" icon="arrow-up-tray" wire:click="$set('showImportModal', true)" class="!rounded-none">Import stock</flux:button>
            <a href="{{ route('flux-admin.inventory-products.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New product</flux:button>
            </a>
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
                <flux:table.column>Catford</flux:table.column>
                <flux:table.column>Tooting</flux:table.column>
                <flux:table.column>Sutton</flux:table.column>
                <flux:table.column>Global</flux:table.column>
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
                        @foreach(['catford_stock', 'tooting_stock', 'sutton_stock'] as $stockField)
                            <flux:table.cell>
                                <div
                                    x-data="{ editing: false, value: @js((int) $r->{$stockField}) }"
                                    class="flex items-center gap-1"
                                >
                                    <input
                                        type="number"
                                        min="0"
                                        x-model.number="value"
                                        :readonly="!editing"
                                        class="h-8 w-20 border border-zinc-200 bg-white px-2 text-right text-sm text-zinc-900 read-only:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:read-only:bg-zinc-950"
                                    />
                                    <button
                                        type="button"
                                        x-show="!editing"
                                        x-on:click="editing = true; $nextTick(() => $el.previousElementSibling.focus())"
                                        class="inline-flex h-8 w-8 items-center justify-center text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                                        title="Edit branch stock"
                                    >
                                        <flux:icon name="pencil-square" class="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        x-cloak
                                        x-show="editing"
                                        x-on:click="$wire.updateBranchStock({{ $r->id }}, '{{ $stockField }}', value).then(() => editing = false)"
                                        class="inline-flex h-8 w-8 items-center justify-center text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950"
                                        title="Save branch stock"
                                    >
                                        <flux:icon name="check" class="h-4 w-4" />
                                    </button>
                                </div>
                            </flux:table.cell>
                        @endforeach
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->global_stock }}</flux:table.cell>
                        <flux:table.cell>
                            @if($r->dead)
                                <x-flux-admin::status-badge status="discontinued" />
                            @else
                                <x-flux-admin::status-badge status="active" />
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.inventory-products.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this product?" icon="trash" class="!rounded-none text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="11" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No products.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showImportModal" class="md:w-[500px]">
        <form wire:submit.prevent="importStock" class="space-y-4" novalidate>
            <flux:heading size="lg">Import stock (XLSX)</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Upload a stock XLSX file. Columns should match the expected format (SKU, branch stock columns).</p>
            <x-flux-admin::field-group label="XLSX file" :error="$errors->first('importFile')" required>
                <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:py-1 file:px-3 file:border file:border-zinc-300 file:text-sm file:bg-white dark:file:bg-zinc-800 dark:file:border-zinc-600 dark:file:text-zinc-300" />
            </x-flux-admin::field-group>
            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="importUpdateZero" class="rounded-none" />
                Update products with zero stock (overwrite with 0)
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showImportModal', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Import</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
