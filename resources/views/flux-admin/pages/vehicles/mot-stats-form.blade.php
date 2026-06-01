<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.mot-stats.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">MOT notifier stats</a>
                <span>/</span>
                <span>{{ $notifier && $notifier->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $notifier && $notifier->exists ? 'Edit MOT notifier' : 'New MOT notifier' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.mot-stats.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Notifier details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('form.customer_name')">
                    <flux:input wire:model="form.customer_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Contact number" :error="$errors->first('form.customer_contact')">
                    <flux:input wire:model="form.customer_contact" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.customer_email')">
                    <flux:input type="email" wire:model="form.customer_email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike reg" required :error="$errors->first('form.motorbike_reg')">
                    <flux:input wire:model="form.motorbike_reg" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="MOT due date" :error="$errors->first('form.mot_due_date')">
                    <flux:input type="date" wire:model="form.mot_due_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax due date" :error="$errors->first('form.tax_due_date')">
                    <flux:input type="date" wire:model="form.tax_due_date" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.mot_notify_email" class="accent-zinc-900 dark:accent-zinc-200"> Notify by email
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.mot_notify_phone" class="accent-zinc-900 dark:accent-zinc-200"> Notify by phone
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.mot-stats.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
