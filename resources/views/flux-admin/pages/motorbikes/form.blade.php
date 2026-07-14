<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbikes.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Motorbikes</a>
                <span>/</span>
                <span>{{ $motorbikeId ? 'Edit motorbike' : 'New motorbike' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motorbikeId ? 'Edit motorbike' : 'New motorbike' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbikes.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save motorbike</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Motorbike details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Reg no" required :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VIN number" :error="$errors->first('form.vin_number')">
                    <flux:input wire:model="form.vin_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" required :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" required :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input wire:model="form.year" type="number" min="1900" max="9999" placeholder="e.g. 2023" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Engine" :error="$errors->first('form.engine')">
                    <flux:input wire:model="form.engine" placeholder="e.g. 125cc" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.color')">
                    <flux:input wire:model="form.color" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Fuel type" :error="$errors->first('form.fuel_type')">
                    <flux:select wire:model="form.fuel_type">
                        <flux:select.option value="Petrol">Petrol</flux:select.option>
                        <flux:select.option value="Diesel">Diesel</flux:select.option>
                        <flux:select.option value="Electric">Electric</flux:select.option>
                        <flux:select.option value="Hybrid">Hybrid</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id">
                        <flux:select.option value="">— none —</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Wheel plan" :error="$errors->first('form.wheel_plan')">
                    <flux:input wire:model="form.wheel_plan" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Type approval" :error="$errors->first('form.type_approval')">
                    <flux:input wire:model="form.type_approval" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Vehicle profile ID" :error="$errors->first('form.vehicle_profile_id')">
                    <flux:input type="number" wire:model="form.vehicle_profile_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Month of first registration" :error="$errors->first('form.month_of_first_registration')">
                    <flux:input type="date" wire:model="form.month_of_first_registration" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date of last V5C issuance" :error="$errors->first('form.date_of_last_v5c_issuance')">
                    <flux:input type="datetime-local" wire:model="form.date_of_last_v5c_issuance" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="CO₂ emissions" :error="$errors->first('form.co2_emissions')">
                    <flux:input wire:model="form.co2_emissions" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Accessories" :error="$errors->first('form.accessories')">
                    <flux:textarea wire:model="form.accessories" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_ebike" class="accent-zinc-900 dark:accent-zinc-200"> E-bike
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.marked_for_export" class="accent-zinc-900 dark:accent-zinc-200"> Marked for export
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbikes.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save motorbike</flux:button>
        </div>
    </form>
</div>
