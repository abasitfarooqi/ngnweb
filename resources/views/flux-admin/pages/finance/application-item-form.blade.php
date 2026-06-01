<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.application-items.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Application items</a>
                <span>/</span>
                <span>{{ $applicationItem && $applicationItem->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $applicationItem && $applicationItem->exists ? 'Edit application item' : 'New application item' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.application-items.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Item details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Application ID" required :error="$errors->first('form.application_id')">
                    <flux:input type="number" wire:model="form.application_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike ID" required :error="$errors->first('form.motorbike_id')">
                    <flux:input type="number" wire:model="form.motorbike_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Weekly instalment (£)" :error="$errors->first('form.weekly_instalment')">
                    <flux:input type="number" step="0.01" wire:model="form.weekly_instalment" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Start date" :error="$errors->first('form.start_date')">
                    <flux:input type="date" wire:model="form.start_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Due date" :error="$errors->first('form.due_date')">
                    <flux:input type="date" wire:model="form.due_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="End date" :error="$errors->first('form.end_date')">
                    <flux:input type="date" wire:model="form.end_date" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_posted" class="accent-zinc-900 dark:accent-zinc-200"> Posted
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.application-items.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
