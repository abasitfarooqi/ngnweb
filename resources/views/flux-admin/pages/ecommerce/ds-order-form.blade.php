<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.ds-orders.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">DS Orders</a>
                <span>/</span>
                <span>{{ $dsOrder ? 'Edit' : 'New order' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $dsOrder ? 'Edit order #'.$dsOrder->id : 'New DS order' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.ds-orders.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save order</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Order details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Full name" required :error="$errors->first('form.full_name')">
                    <flux:input wire:model="form.full_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup date" required :error="$errors->first('form.pick_up_datetime')">
                    <flux:input type="date" wire:model="form.pick_up_datetime" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Postcode" :error="$errors->first('form.postcode')">
                    <flux:input wire:model="form.postcode" class="uppercase" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Address" :error="$errors->first('form.address')">
                    <flux:textarea wire:model="form.address" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.proceed" class="accent-zinc-900 dark:accent-zinc-200"> Proceed
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.ds-orders.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save order</flux:button>
        </div>
    </form>
</div>
