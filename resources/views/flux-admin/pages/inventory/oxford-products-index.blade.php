<div>
    <x-flux-admin::data-table title="Oxford products" description="Supplier catalogue mirrored from Oxford Products.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New product</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search SKU, EAN or description…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.obsolete" placeholder="Obsolete">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="0">Active</flux:select.option>
                        <flux:select.option value="1">Obsolete</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>SKU</flux:table.column>
                <flux:table.column>Description</flux:table.column>
                <flux:table.column>Brand</flux:table.column>
                <flux:table.column>RRP</flux:table.column>
                <flux:table.column>Cost</flux:table.column>
                <flux:table.column>Stock</flux:table.column>
                <flux:table.column>Catford</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="op-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->sku }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-md truncate">{{ $r->description }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->brand }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->rrp_inc_vat, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $r->cost_price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->stock }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->catford_stock }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this product?" icon="trash" class="!rounded-none text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit Oxford product' : 'New Oxford product' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="SKU" :error="$errors->first('formData.sku')" required>
                    <flux:input wire:model="formData.sku" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="EAN" :error="$errors->first('formData.ean')">
                    <flux:input wire:model="formData.ean" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Description" :error="$errors->first('formData.description')" class="md:col-span-2">
                    <flux:input wire:model="formData.description" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Brand" :error="$errors->first('formData.brand')">
                    <flux:input wire:model="formData.brand" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Supplier" :error="$errors->first('formData.supplier')">
                    <flux:input wire:model="formData.supplier" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Supplier code" :error="$errors->first('formData.supplier_code')">
                    <flux:input wire:model="formData.supplier_code" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('formData.colour')">
                    <flux:input wire:model="formData.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Variation" :error="$errors->first('formData.variation')">
                    <flux:input wire:model="formData.variation" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="RRP inc. VAT (£)" :error="$errors->first('formData.rrp_inc_vat')">
                    <flux:input type="number" step="0.01" wire:model="formData.rrp_inc_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="RRP ex. VAT (£)" :error="$errors->first('formData.rrp_less_vat')">
                    <flux:input type="number" step="0.01" wire:model="formData.rrp_less_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Cost price (£)" :error="$errors->first('formData.cost_price')">
                    <flux:input type="number" step="0.01" wire:model="formData.cost_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Stock" :error="$errors->first('formData.stock')">
                    <flux:input type="number" wire:model="formData.stock" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Catford stock" :error="$errors->first('formData.catford_stock')">
                    <flux:input type="number" wire:model="formData.catford_stock" />
                </x-flux-admin::field-group>
            </div>
            <div class="flex gap-6">
                <flux:checkbox wire:model="formData.vatable" label="VATable" />
                <flux:checkbox wire:model="formData.obsolete" label="Obsolete" />
                <flux:checkbox wire:model="formData.dead" label="Dead" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
