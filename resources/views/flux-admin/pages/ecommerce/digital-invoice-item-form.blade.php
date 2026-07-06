<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.digital-invoice-items.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Digital invoice items</a>
                <span>/</span>
                <span>{{ $invoiceItem && $invoiceItem->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $invoiceItem && $invoiceItem->exists ? 'Edit invoice item' : 'New invoice item' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.digital-invoice-items.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Item details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Invoice ID" required :error="$errors->first('form.invoice_id')">
                    <flux:input type="number" wire:model="form.invoice_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Item name" required :error="$errors->first('form.item_name')">
                    <flux:input wire:model="form.item_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SKU" :error="$errors->first('form.sku')">
                    <flux:input wire:model="form.sku" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Quantity" required :error="$errors->first('form.quantity')">
                    <flux:input type="number" step="0.01" wire:model="form.quantity" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Unit price (£)" required :error="$errors->first('form.price')">
                    <flux:input type="number" step="0.01" wire:model="form.price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Discount (£)" :error="$errors->first('form.discount')">
                    <flux:input type="number" step="0.01" wire:model="form.discount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax (£)" :error="$errors->first('form.tax')">
                    <flux:input type="number" step="0.01" wire:model="form.tax" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.digital-invoice-items.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
