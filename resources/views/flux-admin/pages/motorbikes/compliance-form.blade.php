<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-compliance.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Vehicle compliance</a>
                <span>/</span>
                <span>{{ $compliance && $compliance->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $compliance && $compliance->exists ? 'Edit compliance record' : 'New compliance record' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-compliance.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Compliance details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Motorbike ID" required :error="$errors->first('form.motorbike_id')">
                    <flux:input type="number" wire:model="form.motorbike_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" required :error="$errors->first('form.year')">
                    <flux:input wire:model="form.year" placeholder="e.g. 2024" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">MOT</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="MOT status" :error="$errors->first('form.mot_status')">
                    <flux:select wire:model="form.mot_status" placeholder="— Select —">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="MOT due date" :error="$errors->first('form.mot_due_date')">
                    <flux:input type="date" wire:model="form.mot_due_date" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Road tax</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Road tax status" :error="$errors->first('form.road_tax_status')">
                    <flux:select wire:model="form.road_tax_status" placeholder="— Select —">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax due date" :error="$errors->first('form.tax_due_date')">
                    <flux:input type="date" wire:model="form.tax_due_date" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Insurance</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Insurance status" :error="$errors->first('form.insurance_status')">
                    <flux:select wire:model="form.insurance_status" placeholder="— Select —">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Insurance due date" :error="$errors->first('form.insurance_due_date')">
                    <flux:input type="date" wire:model="form.insurance_due_date" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-compliance.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
