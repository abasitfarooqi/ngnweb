<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.ds-order-items.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">DS order items</a>
                <span>/</span>
                <span>{{ $dsOrderItem && $dsOrderItem->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $dsOrderItem && $dsOrderItem->exists ? 'Edit DS order item' : 'New DS order item' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.ds-order-items.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Item details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="DS order ID" required :error="$errors->first('form.ds_order_id')">
                    <flux:input type="number" wire:model="form.ds_order_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.vrm')">
                    <flux:input wire:model="form.vrm" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Distance (miles)" :error="$errors->first('form.distance')">
                    <flux:input type="number" step="0.1" wire:model="form.distance" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup lat" :error="$errors->first('form.pickup_lat')">
                    <flux:input type="number" step="0.000001" wire:model="form.pickup_lat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup lon" :error="$errors->first('form.pickup_lon')">
                    <flux:input type="number" step="0.000001" wire:model="form.pickup_lon" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff lat" :error="$errors->first('form.dropoff_lat')">
                    <flux:input type="number" step="0.000001" wire:model="form.dropoff_lat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff lon" :error="$errors->first('form.dropoff_lon')">
                    <flux:input type="number" step="0.000001" wire:model="form.dropoff_lon" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Pickup address" required :error="$errors->first('form.pickup_address')">
                    <flux:textarea wire:model="form.pickup_address" rows="2" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff address" required :error="$errors->first('form.dropoff_address')">
                    <flux:textarea wire:model="form.dropoff_address" rows="2" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup postcode" required :error="$errors->first('form.pickup_postcode')">
                    <flux:input wire:model="form.pickup_postcode" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff postcode" required :error="$errors->first('form.dropoff_postcode')">
                    <flux:input wire:model="form.dropoff_postcode" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.moveable" class="accent-zinc-900 dark:accent-zinc-200"> Moveable
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.documents" class="accent-zinc-900 dark:accent-zinc-200"> Documents present
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.keys" class="accent-zinc-900 dark:accent-zinc-200"> Keys present
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.ds-order-items.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
