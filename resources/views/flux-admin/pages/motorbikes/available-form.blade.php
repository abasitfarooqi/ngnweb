<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.backpack.motorbike-available.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Motorbike available</a>
                <span>/</span>
                <span>Edit</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Edit eligibility — {{ $motorbike->reg_no }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.backpack.motorbike-available.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Motorbike</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Reg No" required :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" readonly />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Vehicle profile" hint="Must be 1 for active renting eligibility" :error="$errors->first('form.vehicle_profile_id')">
                    <flux:select wire:model="form.vehicle_profile_id" placeholder="— Select —">
                        <flux:select.option value="">— Select —</flux:select.option>
                        @foreach($profiles as $id => $name)
                            <flux:select.option value="{{ $id }}">{{ $name }} ({{ $id }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Compliance</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="MOT status" required hint="Valid or No details held by DVLA" :error="$errors->first('form.mot_status')">
                    <flux:select wire:model="form.mot_status" placeholder="— Select —">
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Road tax status" required hint="Must be Taxed" :error="$errors->first('form.road_tax_status')">
                    <flux:select wire:model="form.road_tax_status" placeholder="— Select —">
                        <flux:select.option value="Taxed">Taxed</flux:select.option>
                        <flux:select.option value="SORN">SORN</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Pricing</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Weekly price (£)" required :error="$errors->first('form.weekly_price')">
                    <flux:input type="number" step="any" wire:model="form.weekly_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Is current pricing" hint="Must be current for eligibility" :error="$errors->first('form.iscurrent')">
                    <flux:checkbox wire:model="form.iscurrent" label="Current pricing" />
                </x-flux-admin::field-group>
            </div>
        </div>
    </form>
</div>
