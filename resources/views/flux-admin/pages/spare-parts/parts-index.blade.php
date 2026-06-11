<div>
    <x-flux-admin::data-table title="Spare parts · Parts" description="Individual parts in the spare parts catalogue.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.sp-parts.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New part</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search part # or name…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.stock_status" placeholder="Stock">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="in_stock">In stock</flux:select.option>
                        <flux:select.option value="low_stock">Low stock</flux:select.option>
                        <flux:select.option value="out_of_stock">Out of stock</flux:select.option>
                        <flux:select.option value="discontinued">Discontinued</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.is_active" placeholder="Active">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Inactive</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Part #</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Stock status</flux:table.column>
                <flux:table.column>Catford</flux:table.column>
                <flux:table.column>Tooting</flux:table.column>
                <flux:table.column>Sutton</flux:table.column>
                <flux:table.column>Global</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Synced</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="spp-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->part_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->name }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->stock_status" /></flux:table.cell>
                        @foreach(['catford_stock', 'tooting_stock', 'sutton_stock'] as $stockField)
                            <flux:table.cell>
                                <div
                                    x-data="{ editing: false, value: @js((float) $r->{$stockField}) }"
                                    class="flex items-center gap-1"
                                >
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
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
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->global_stock ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->price_gbp_inc_vat, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->last_synced_at?->format('d M H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.sp-parts.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this part?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
