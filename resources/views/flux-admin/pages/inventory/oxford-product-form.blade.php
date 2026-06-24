<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.oxford-products.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Oxford products</a>
                <span>/</span>
                <span>{{ $oxfordProduct && $oxfordProduct->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $oxfordProduct && $oxfordProduct->exists ? 'Edit Oxford product' : 'New Oxford product' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.oxford-products.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Product details</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-flux-admin::field-group label="SKU" required :error="$errors->first('form.sku')">
                    <flux:input wire:model="form.sku" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="EAN" :error="$errors->first('form.ean')">
                    <flux:input wire:model="form.ean" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Description" class="md:col-span-2" :error="$errors->first('form.description')">
                    <flux:input wire:model="form.description" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Brand" :error="$errors->first('form.brand')">
                    <flux:input wire:model="form.brand" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Supplier" :error="$errors->first('form.supplier')">
                    <flux:input wire:model="form.supplier" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Supplier code" :error="$errors->first('form.supplier_code')">
                    <flux:input wire:model="form.supplier_code" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Variation" :error="$errors->first('form.variation')">
                    <flux:input wire:model="form.variation" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="RRP inc. VAT (£)" :error="$errors->first('form.rrp_inc_vat')">
                    <flux:input type="number" step="0.01" wire:model="form.rrp_inc_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="RRP ex. VAT (£)" :error="$errors->first('form.rrp_less_vat')">
                    <flux:input type="number" step="0.01" wire:model="form.rrp_less_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Cost price (£)" :error="$errors->first('form.cost_price')">
                    <flux:input type="number" step="0.01" wire:model="form.cost_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Stock" :error="$errors->first('form.stock')">
                    <flux:input type="number" wire:model="form.stock" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Catford stock" :error="$errors->first('form.catford_stock')">
                    <flux:input type="number" wire:model="form.catford_stock" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <flux:checkbox wire:model="form.vatable" label="VATable" />
                <flux:checkbox wire:model="form.obsolete" label="Obsolete" />
                <flux:checkbox wire:model="form.dead" label="Dead" />
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.oxford-products.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
