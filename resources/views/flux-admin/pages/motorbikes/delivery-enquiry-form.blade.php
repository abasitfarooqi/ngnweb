<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.delivery-enquiries.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200">Delivery enquiries</a>
                <span>/</span>
                <span>{{ $deliveryEnquiry && $deliveryEnquiry->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $deliveryEnquiry && $deliveryEnquiry->exists ? 'Edit enquiry' : 'New delivery enquiry' }}
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Distance and cost use the same Geoapify calculation as ngn-admin.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.delivery-enquiries.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Route</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                <x-flux-admin::field-group label="Pickup postcode" required :error="$errors->first('form.pickup_postcode')">
                    <flux:input wire:model.blur="form.pickup_postcode" class="uppercase" placeholder="e.g. SE6 4NU" autocomplete="off" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff postcode" required :error="$errors->first('form.dropoff_postcode')">
                    <flux:input wire:model.blur="form.dropoff_postcode" class="uppercase" placeholder="e.g. SW16 6RE" autocomplete="off" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup date/time" required :error="$errors->first('form.pick_up_datetime')">
                    <flux:input type="datetime-local" wire:model="form.pick_up_datetime" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Vehicle type" required :error="$errors->first('form.vehicle_type_id')">
                    <flux:select wire:model="form.vehicle_type_id" placeholder="Select type">
                        <flux:select.option value="">— Select —</flux:select.option>
                        @foreach($vehicleTypes as $type)
                            <flux:select.option value="{{ $type->id }}">{{ $type->name }}@if($type->cc_range) ({{ $type->cc_range }})@endif</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup address" :error="$errors->first('form.pickup_address')">
                    <flux:input wire:model="form.pickup_address" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Dropoff address" :error="$errors->first('form.dropoff_address')">
                    <flux:input wire:model="form.dropoff_address" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.moveable" class="accent-zinc-900 dark:accent-zinc-200"> Moveable
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.documents" class="accent-zinc-900 dark:accent-zinc-200"> Documents
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.keys" class="accent-zinc-900 dark:accent-zinc-200"> Keys
                </label>
                <flux:button
                    type="button"
                    wire:click="recalculateDistance"
                    wire:loading.attr="disabled"
                    wire:target="recalculateDistance"
                    variant="ghost"
                    size="sm"
                    class="!rounded-none"
                    icon="map"
                >
                    <span wire:loading.remove wire:target="recalculateDistance">Calculate distance</span>
                    <span wire:loading wire:target="recalculateDistance">Calculating…</span>
                </flux:button>
            </div>

            <div class="flux-admin-form-grid mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                <x-flux-admin::field-group label="Distance (miles)" :error="$errors->first('form.distance')">
                    <flux:input type="number" step="0.01" wire:model="form.distance" readonly />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total cost (£)" :error="$errors->first('form.total_cost')">
                    <flux:input type="number" step="0.01" wire:model="form.total_cost" readonly />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Customer</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
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
                    <flux:input wire:model="form.vrm" class="uppercase" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="— Any —">
                        <flux:select.option value="">— Any —</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer postcode" :error="$errors->first('form.customer_postcode')">
                    <flux:input wire:model="form.customer_postcode" class="uppercase" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Customer address" :error="$errors->first('form.customer_address')">
                    <flux:textarea wire:model="form.customer_address" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>
            @if($deliveryEnquiry && $deliveryEnquiry->exists)
                <div class="mt-4">
                    <x-flux-admin::field-group label="Internal notes" :error="$errors->first('form.notes')">
                        <flux:textarea wire:model="form.notes" rows="2" />
                    </x-flux-admin::field-group>
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="form.is_dealt" class="accent-zinc-900 dark:accent-zinc-200"> Dealt with
                    </label>
                </div>
            @endif
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="sendEmail" class="accent-zinc-900 dark:accent-zinc-200"> Send email on save
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
