<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.inventory-products.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Products</a>
                <span>/</span>
                <span>{{ $product ? 'Edit' : 'New product' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $product ? 'Edit product: '.$product->name : 'New product' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.inventory-products.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save product</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Product details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SKU" :error="$errors->first('form.sku')">
                    <flux:input wire:model="form.sku" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="EAN" :error="$errors->first('form.ean')">
                    <flux:input wire:model="form.ean" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Variation" :error="$errors->first('form.variation')">
                    <flux:input wire:model="form.variation" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Brand" :error="$errors->first('form.brand_id')">
                    <flux:select wire:model="form.brand_id" placeholder="Select brand">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($brands as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('form.category_id')">
                    <flux:select wire:model="form.category_id" placeholder="Select category">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($categories as $c)
                            <flux:select.option value="{{ $c->id }}">{{ $c->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model_id')">
                    <flux:select wire:model="form.model_id" placeholder="Select model">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($models as $m)
                            <flux:select.option value="{{ $m->id }}">{{ $m->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Normal price (£)" :error="$errors->first('form.normal_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.normal_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS price (£)" :error="$errors->first('form.pos_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.pos_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS VAT (£)" :error="$errors->first('form.pos_vat')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.pos_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Global stock" :error="$errors->first('form.global_stock')">
                    <flux:input type="number" wire:model="form.global_stock" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Slug" :error="$errors->first('form.slug')">
                    <flux:input wire:model="form.slug" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Meta title" :error="$errors->first('form.meta_title')">
                    <flux:input wire:model="form.meta_title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Meta description" :error="$errors->first('form.meta_description')">
                    <flux:input wire:model="form.meta_description" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" rows="4" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.vatable" class="accent-zinc-900 dark:accent-zinc-200"> Vatable
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_oxford" class="accent-zinc-900 dark:accent-zinc-200"> Oxford product
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_ecommerce" class="accent-zinc-900 dark:accent-zinc-200"> Visible on shop
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.dead" class="accent-zinc-900 dark:accent-zinc-200"> Dead (discontinued)
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.inventory-products.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save product</flux:button>
        </div>
    </form>
</div>
