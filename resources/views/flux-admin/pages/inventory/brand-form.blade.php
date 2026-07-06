<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.inventory-brands.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Brands</a>
                <span>/</span>
                <span>{{ $brand ? 'Edit' : 'New brand' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $brand ? 'Edit brand: '.$brand->name : 'New brand' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.inventory-brands.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save brand</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Brand details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Slug" :error="$errors->first('form.slug')" hint="Leave empty to auto-generate from name.">
                    <flux:input wire:model="form.slug" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sort order" :error="$errors->first('form.sort_order')">
                    <flux:input type="number" wire:model="form.sort_order" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Image URL" :error="$errors->first('form.image_url')">
                    <flux:input wire:model="form.image_url" placeholder="https://…" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Meta title" :error="$errors->first('form.meta_title')">
                    <flux:input wire:model="form.meta_title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Meta description" :error="$errors->first('form.meta_description')">
                    <flux:input wire:model="form.meta_description" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_ecommerce" class="accent-zinc-900 dark:accent-zinc-200"> Visible on shop
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.inventory-brands.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save brand</flux:button>
        </div>
    </form>
</div>
