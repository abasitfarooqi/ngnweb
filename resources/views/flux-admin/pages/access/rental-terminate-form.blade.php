<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.rental-terminate-links.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Rental terminate links</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit terminate link' : 'New terminate link' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.rental-terminate-links.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Link details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Customer ID" required :error="$errors->first('form.customer_id')">
                    <flux:input type="number" wire:model="form.customer_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Booking ID" required :error="$errors->first('form.booking_id')">
                    <flux:input type="number" wire:model="form.booking_id" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Passcode" required :error="$errors->first('form.passcode')">
                    <div class="flex gap-2">
                        <flux:input wire:model="form.passcode" class="flex-1" />
                        <flux:button type="button" size="sm" variant="ghost" wire:click="regeneratePasscode" icon="arrow-path" class="!rounded-none">Regenerate</flux:button>
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Expires at" required :error="$errors->first('form.expire_at')">
                    <flux:input type="datetime-local" wire:model="form.expire_at" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.rental-terminate-links.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
