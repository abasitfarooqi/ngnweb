<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.judopay-subscriptions.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Judopay subscriptions</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit subscription' : 'New subscription' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.judopay-subscriptions.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Subscription details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="paused">Paused</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Billing frequency" required :error="$errors->first('form.billing_frequency')">
                    <flux:select wire:model="form.billing_frequency">
                        <flux:select.option value="weekly">Weekly</flux:select.option>
                        <flux:select.option value="monthly">Monthly</flux:select.option>
                        <flux:select.option value="annually">Annually</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Billing day" :error="$errors->first('form.billing_day')">
                    <flux:input type="number" wire:model="form.billing_day" placeholder="e.g. 1, 15, 28" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Amount (£)" :error="$errors->first('form.amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Start date" :error="$errors->first('form.start_date')">
                    <flux:input type="date" wire:model="form.start_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="End date" :error="$errors->first('form.end_date')">
                    <flux:input type="date" wire:model="form.end_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Judopay onboarding ID" :error="$errors->first('form.judopay_onboarding_id')">
                    <flux:input type="number" wire:model="form.judopay_onboarding_id" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Consumer reference" :error="$errors->first('form.consumer_reference')">
                    <flux:input wire:model="form.consumer_reference" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.judopay-subscriptions.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
