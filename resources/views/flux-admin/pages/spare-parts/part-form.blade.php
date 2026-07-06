<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.sp-parts.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Parts</a>
                <span>/</span>
                <span>{{ $spPart ? 'Edit' : 'New part' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $spPart ? 'Edit part: '.$spPart->part_number : 'New spare part' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.sp-parts.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save part</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Part details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Part number" required :error="$errors->first('form.part_number')">
                    <flux:input wire:model="form.part_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Stock status" :error="$errors->first('form.stock_status')">
                    <flux:select wire:model="form.stock_status">
                        <flux:select.option value="in_stock">In stock</flux:select.option>
                        <flux:select.option value="out_of_stock">Out of stock</flux:select.option>
                        <flux:select.option value="discontinued">Discontinued</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price inc. VAT (£)" :error="$errors->first('form.price_gbp_inc_vat')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.price_gbp_inc_vat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Global stock" :error="$errors->first('form.global_stock')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.global_stock" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.sp-parts.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save part</flux:button>
        </div>
    </form>
</div>
