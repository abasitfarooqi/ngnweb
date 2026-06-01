<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.inventory-partners.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Partners</a>
                <span>/</span>
                <span>{{ $partner ? 'Edit' : 'New partner' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $partner ? 'Edit partner: '.$partner->companyname : 'New partner' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.inventory-partners.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save partner</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Company details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Company name" required :error="$errors->first('form.companyname')">
                    <flux:input wire:model="form.companyname" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Company number" :error="$errors->first('form.company_number')">
                    <flux:input wire:model="form.company_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Website" :error="$errors->first('form.website')">
                    <flux:input wire:model="form.website" placeholder="https://…" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="First name" :error="$errors->first('form.first_name')">
                    <flux:input wire:model="form.first_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Last name" :error="$errors->first('form.last_name')">
                    <flux:input wire:model="form.last_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Mobile" :error="$errors->first('form.mobile')">
                    <flux:input wire:model="form.mobile" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Fleet size" :error="$errors->first('form.fleet_size')">
                    <flux:input type="number" min="0" wire:model="form.fleet_size" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Operating since" :error="$errors->first('form.operating_since')">
                    <flux:input type="date" wire:model="form.operating_since" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Company address" :error="$errors->first('form.company_address')">
                    <flux:textarea wire:model="form.company_address" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_approved" class="accent-zinc-900 dark:accent-zinc-200"> Approved
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.inventory-partners.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save partner</flux:button>
        </div>
    </form>
</div>
