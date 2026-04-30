<div>
    <x-flux-admin::data-table title="Spare parts · Parts" description="Individual parts in the spare parts catalogue.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New part</flux:button>
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
                <flux:table.column>Stock</flux:table.column>
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
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->global_stock ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->price_gbp_inc_vat, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->last_synced_at?->format('d M H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this part?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[640px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit part' : 'New part' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Part number" :error="$errors->first('formData.part_number')" required>
                    <flux:input wire:model="formData.part_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Name" :error="$errors->first('formData.name')" required>
                    <flux:input wire:model="formData.name" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Note" :error="$errors->first('formData.note')">
                <flux:textarea wire:model="formData.note" rows="3" />
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Stock status" :error="$errors->first('formData.stock_status')">
                    <flux:select wire:model="formData.stock_status">
                        <flux:select.option value="in_stock">In stock</flux:select.option>
                        <flux:select.option value="low_stock">Low stock</flux:select.option>
                        <flux:select.option value="out_of_stock">Out of stock</flux:select.option>
                        <flux:select.option value="discontinued">Discontinued</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price (inc VAT)" :error="$errors->first('formData.price_gbp_inc_vat')">
                    <flux:input type="number" step="0.01" wire:model="formData.price_gbp_inc_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Global stock" :error="$errors->first('formData.global_stock')">
                    <flux:input type="number" step="0.01" wire:model="formData.global_stock" />
                </x-flux-admin::field-group>
            </div>
            <flux:checkbox wire:model="formData.is_active" label="Active" />
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
