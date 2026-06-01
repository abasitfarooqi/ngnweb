<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.vehicle-notifications.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Vehicle notifications</a>
                <span>/</span>
                <span>{{ $vehicleNotification && $vehicleNotification->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $vehicleNotification && $vehicleNotification->exists ? 'Edit notification subscriber' : 'New notification subscriber' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.vehicle-notifications.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Subscriber details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="First name" required :error="$errors->first('form.first_name')">
                    <flux:input wire:model="form.first_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Last name" required :error="$errors->first('form.last_name')">
                    <flux:input wire:model="form.last_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Registration" :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.notify_email" class="accent-zinc-900 dark:accent-zinc-200"> Notify by email
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.notify_phone" class="accent-zinc-900 dark:accent-zinc-200"> Notify by phone
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.enable" class="accent-zinc-900 dark:accent-zinc-200"> Enabled
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.vehicle-notifications.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
