<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.used-purchases.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Used vehicle purchases</a>
                <span>/</span>
                <span>{{ $purchaseUsed && $purchaseUsed->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $purchaseUsed && $purchaseUsed->exists ? 'Edit purchase record' : 'New used vehicle purchase' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.used-purchases.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Seller details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Purchase date" :error="$errors->first('form.purchase_date')">
                    <flux:input type="date" wire:model="form.purchase_date" />
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
                <x-flux-admin::field-group label="Postcode" :error="$errors->first('form.postcode')">
                    <flux:input wire:model="form.postcode" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Address" :error="$errors->first('form.address')">
                    <flux:textarea wire:model="form.address" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Vehicle details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Make" :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input wire:model="form.year" placeholder="e.g. 2020" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Reg number" :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VIN" :error="$errors->first('form.vin')">
                    <flux:input wire:model="form.vin" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Financials</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Price (£)" :error="$errors->first('form.price')">
                    <flux:input type="number" step="0.01" wire:model="form.price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Deposit (£)" :error="$errors->first('form.deposit')">
                    <flux:input type="number" step="0.01" wire:model="form.deposit" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Outstanding (£)" :error="$errors->first('form.outstanding')">
                    <flux:input type="number" step="0.01" wire:model="form.outstanding" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.used-purchases.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
