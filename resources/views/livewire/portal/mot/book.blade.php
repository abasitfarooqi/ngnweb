<div>
    <flux:heading size="xl" class="mb-6">Book MOT Appointment</flux:heading>
    <flux:callout variant="info" icon="information-circle" class="mb-6">
        <flux:callout.text>Catford only. Sundays are closed and reserved time slots are removed automatically for each booking date.</flux:callout.text>
    </flux:callout>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if($activeCustomerBooking)
        <flux:callout variant="warning" icon="information-circle" class="mb-6">
            <flux:callout.text>
                You already have a pending booking for {{ $activeCustomerBooking['registration'] }} on {{ $activeCustomerBooking['date'] }} at {{ $activeCustomerBooking['time'] }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit.prevent="submit" class="site-form site-form-stack">
        <flux:card class="p-6 space-y-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Appointment Details</h2>

            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Date of Appointment *</flux:label>
                    <x-site.booking-date-picker wire:model.live="date_of_appointment" min="{{ \App\Support\BookingSchedule::minBookableDate(true) }}" />
                    <flux:error name="date_of_appointment" />
                </flux:field>
                <flux:field>
                    <flux:label>Time Slot *</flux:label>
                    <flux:select wire:model="time_slot" variant="listbox" placeholder="Select time slot">
                        @foreach($this->availableTimeSlots as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="time_slot" />
                </flux:field>
            </x-site.form-grid>

            <flux:field>
                <flux:label>Branch Location *</flux:label>
                <flux:input value="Catford" disabled />
                <flux:error name="branch_id" />
            </flux:field>

            <flux:separator />

            <h3 class="text-base font-medium text-gray-900 dark:text-white">Vehicle Details</h3>
            <x-site.form-grid :cols="3">
                <flux:field>
                    <flux:label>Registration Number *</flux:label>
                    <flux:input wire:model="motorbike_reg_no" placeholder="AB12 CDE" class="uppercase" />
                    <flux:error name="motorbike_reg_no" />
                </flux:field>
                <flux:field>
                    <flux:label>Make *</flux:label>
                    <flux:input wire:model="motorbike_make" placeholder="Honda" />
                    <flux:error name="motorbike_make" />
                </flux:field>
                <flux:field>
                    <flux:label>Model *</flux:label>
                    <flux:input wire:model="motorbike_model" placeholder="PCX 125" />
                    <flux:error name="motorbike_model" />
                </flux:field>
            </x-site.form-grid>

            <flux:separator />

            <flux:field>
                <flux:label>Additional Notes</flux:label>
                <flux:textarea wire:model="notes" rows="4" placeholder="Any special requirements or concerns…" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-between items-center pt-2">
                <flux:button href="{{ route('account.bookings') }}" variant="outline">Cancel</flux:button>
                <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                    <span wire:loading.remove>Book Appointment</span>
                    <span wire:loading>Booking…</span>
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
