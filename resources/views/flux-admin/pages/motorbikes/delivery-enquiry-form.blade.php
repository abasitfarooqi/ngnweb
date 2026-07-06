<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.delivery-enquiries.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Delivery enquiries</a>
                <span>/</span>
                <span>{{ $deliveryEnquiry && $deliveryEnquiry->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $deliveryEnquiry && $deliveryEnquiry->exists ? 'Edit enquiry' : 'New delivery enquiry' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.delivery-enquiries.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Contact details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Full name" required :error="$errors->first('form.full_name')">
                    <flux:input wire:model="form.full_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.vrm')">
                    <flux:input wire:model="form.vrm" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup postcode" :error="$errors->first('form.pickup_postcode')">
                    <flux:input wire:model="form.pickup_postcode" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff postcode" :error="$errors->first('form.dropoff_postcode')">
                    <flux:input wire:model="form.dropoff_postcode" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup date/time" :error="$errors->first('form.pick_up_datetime')">
                    <flux:input type="datetime-local" wire:model="form.pick_up_datetime" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Distance (miles)" :error="$errors->first('form.distance')">
                    <flux:input type="number" step="0.1" wire:model="form.distance" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total cost (£)" :error="$errors->first('form.total_cost')">
                    <flux:input type="number" step="0.01" wire:model="form.total_cost" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="— Any —">
                        <flux:select.option value="">— Any —</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Pickup address" :error="$errors->first('form.pickup_address')">
                    <flux:textarea wire:model="form.pickup_address" rows="2" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff address" :error="$errors->first('form.dropoff_address')">
                    <flux:textarea wire:model="form.dropoff_address" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_dealt" class="accent-zinc-900 dark:accent-zinc-200"> Dealt with
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.delivery-enquiries.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
