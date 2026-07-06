<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.vehicle-issuances.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Vehicle issuances</a>
                <span>/</span>
                <span>{{ $vehicleIssuance && $vehicleIssuance->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $vehicleIssuance && $vehicleIssuance->exists ? 'Edit issuance' : 'New vehicle issuance' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.vehicle-issuances.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Issuance details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Issue date" required :error="$errors->first('form.issue_date')">
                    <flux:input type="date" wire:model="form.issue_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike ID" required :error="$errors->first('form.motorbike_id')">
                    <flux:input type="number" wire:model="form.motorbike_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer ID" :error="$errors->first('form.customer_id')">
                    <flux:input type="number" wire:model="form.customer_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="User ID" required :error="$errors->first('form.user_id')">
                    <flux:input type="number" wire:model="form.user_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch ID" :error="$errors->first('form.branch_id')">
                    <flux:input type="number" wire:model="form.branch_id" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_returned" class="accent-zinc-900 dark:accent-zinc-200"> Returned
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.vehicle-issuances.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
