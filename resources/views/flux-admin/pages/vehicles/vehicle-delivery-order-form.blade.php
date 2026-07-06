<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.vehicle-delivery-orders.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Vehicle delivery orders</a>
                <span>/</span>
                <span>{{ $vehicleDeliveryOrder && $vehicleDeliveryOrder->exists ? 'Edit #'.$vehicleDeliveryOrder->id : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $vehicleDeliveryOrder && $vehicleDeliveryOrder->exists ? 'Edit delivery order #'.$vehicleDeliveryOrder->id : 'New delivery order' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.vehicle-delivery-orders.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Order details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Quote date" required :error="$errors->first('form.quote_date')">
                    <flux:input type="date" wire:model="form.quote_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup date" :error="$errors->first('form.pickup_date')">
                    <flux:input type="date" wire:model="form.pickup_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Full name" required :error="$errors->first('form.full_name')">
                    <flux:input wire:model="form.full_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone number" :error="$errors->first('form.phone_number')">
                    <flux:input wire:model="form.phone_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.vrm')">
                    <flux:input wire:model="form.vrm" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Vehicle type" required :error="$errors->first('form.delivery_vehicle_type_id')">
                    <flux:select wire:model="form.delivery_vehicle_type_id" placeholder="— Select —">
                        @foreach($types as $t)
                            <flux:select.option value="{{ $t->id }}">{{ $t->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="— Any —">
                        <flux:select.option value="">— Any —</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total distance (miles)" :error="$errors->first('form.total_distance')">
                    <flux:input type="number" step="0.1" wire:model="form.total_distance" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Surcharge (£)" :error="$errors->first('form.surcharge')">
                    <flux:input type="number" step="0.01" wire:model="form.surcharge" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.vehicle-delivery-orders.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
