<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.service-bookings.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Service bookings</a>
                <span>/</span>
                <span>{{ $serviceBooking && $serviceBooking->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $serviceBooking && $serviceBooking->exists ? 'Edit service booking' : 'New service booking' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            @if($serviceBooking && $serviceBooking->exists && ! ($form['is_dealt'] ?? false))
                <flux:button wire:click="markAsDealt" variant="filled" size="sm" icon="check" class="!rounded-none">
                    Mark as dealt
                </flux:button>
            @elseif($serviceBooking && $serviceBooking->exists && ($form['is_dealt'] ?? false))
                <flux:badge color="green" size="sm">Dealt</flux:badge>
            @endif
            <a href="{{ route('flux-admin.service-bookings.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Booking details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('form.fullname')">
                    <flux:input wire:model="form.fullname" placeholder="Full name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" placeholder="Phone number" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" placeholder="Email address" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" placeholder="Registration" class="uppercase" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Enquiry type" :error="$errors->first('form.enquiry_type')">
                    <flux:select wire:model="form.enquiry_type">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="service_booking">Service booking</flux:select.option>
                        <flux:select.option value="general">General</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Service type" :error="$errors->first('form.service_type')">
                    <flux:input wire:model="form.service_type" placeholder="e.g. oil change" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Booking date" :error="$errors->first('form.booking_date')">
                    <flux:input type="date" wire:model="form.booking_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Booking time" :error="$errors->first('form.booking_time')">
                    <flux:input wire:model="form.booking_time" placeholder="e.g. 10:00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Status" :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="confirmed">Confirmed</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Subject" :error="$errors->first('form.subject')">
                    <flux:input wire:model="form.subject" placeholder="Subject" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" placeholder="Describe the issue…" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" placeholder="Internal notes…" rows="2" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                <flux:checkbox wire:model="form.is_dealt" label="Mark as dealt" />
                @if($serviceBooking && $serviceBooking->exists && $serviceBooking->user)
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                        Dealt by {{ trim($serviceBooking->user->first_name.' '.$serviceBooking->user->last_name) }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.service-bookings.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
