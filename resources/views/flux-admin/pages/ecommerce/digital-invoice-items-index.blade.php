<div>
    <x-flux-admin::data-table title="Digital invoice items" description="Line items attached to digital invoices.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New item</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, SKU or invoice ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.invoice_id" placeholder="Invoice ID" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>Item</flux:table.column>
                <flux:table.column>SKU</flux:table.column>
                <flux:table.column>Qty</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="dii-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->invoice_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->item_name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->sku ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->quantity }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $r->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $r->total, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this item?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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
            <flux:heading size="lg">{{ $recordId ? 'Edit item' : 'New invoice item' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Invoice ID" :error="$errors->first('formData.invoice_id')" required>
                    <flux:input type="number" wire:model="formData.invoice_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SKU" :error="$errors->first('formData.sku')">
                    <flux:input wire:model="formData.sku" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Item name" :error="$errors->first('formData.item_name')" required>
                <flux:input wire:model="formData.item_name" />
            </x-flux-admin::field-group>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-flux-admin::field-group label="Quantity" :error="$errors->first('formData.quantity')" required>
                    <flux:input type="number" step="0.01" wire:model="formData.quantity" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price (£)" :error="$errors->first('formData.price')" required>
                    <flux:input type="number" step="0.01" wire:model="formData.price" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Discount" :error="$errors->first('formData.discount')">
                    <flux:input type="number" step="0.01" wire:model="formData.discount" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax" :error="$errors->first('formData.tax')">
                    <flux:input type="number" step="0.01" wire:model="formData.tax" min="0" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Notes" :error="$errors->first('formData.notes')">
                <flux:textarea wire:model="formData.notes" rows="2" />
            </x-flux-admin::field-group>
            <flux:callout icon="information-circle">Total is auto-calculated as <span class="font-mono">(qty × price) − discount + tax</span>.</flux:callout>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
