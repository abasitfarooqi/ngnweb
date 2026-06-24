<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.customer-appointments.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Customer appointments</a>
                <span>/</span>
                <span>{{ $customerAppointment && $customerAppointment->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $customerAppointment && $customerAppointment->exists ? 'Edit appointment' : 'New appointment' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.customer-appointments.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Appointment details</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-flux-admin::field-group label="Appointment date" required :error="$errors->first('form.appointment_date')" class="sm:col-span-2">
                    <flux:input type="datetime-local" wire:model="form.appointment_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('form.customer_name')">
                    <flux:input wire:model="form.customer_name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Registration" :error="$errors->first('form.registration_number')">
                    <flux:input wire:model="form.registration_number" class="uppercase" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Contact number" :error="$errors->first('form.contact_number')">
                    <flux:input wire:model="form.contact_number" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Booking reason" :error="$errors->first('form.booking_reason')" class="sm:col-span-2">
                    <flux:textarea wire:model="form.booking_reason" rows="4" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_resolved" class="accent-zinc-900 dark:accent-zinc-200"> Mark as resolved
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="sendEmail" class="accent-zinc-900 dark:accent-zinc-200"> Email the customer after saving
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.customer-appointments.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
