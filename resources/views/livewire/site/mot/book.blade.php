<div>
{{-- Hero --}}
<div class="site-page-hero py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-2">Book Your MOT Test</h1>
        <p class="text-gray-300">MOT Booking at our Catford branch</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:callout variant="info" icon="information-circle" class="mb-6">
        <flux:callout.text>Catford only. Sundays are closed and booked time slots cannot be selected again.</flux:callout.text>
    </flux:callout>

    @if($this->activeCustomerBooking)
        <flux:callout variant="warning" icon="information-circle" class="mb-6">
            <flux:callout.text>
                We already have a MOT booking for {{ $this->activeCustomerBooking['registration'] }} on {{ $this->activeCustomerBooking['date'] }} at {{ $this->activeCustomerBooking['time'] }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    <x-site.form-panel title="MOT booking form">
        <form wire:key="mot-book-{{ $formNonce }}" wire:submit="submitBooking" class="site-form site-form-stack">

            <flux:field>
                <flux:label>Branch</flux:label>
                <flux:input value="{{ $branchLabel }}" disabled />
                <flux:error name="branch_id" />
            </flux:field>

            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Registration Number *</flux:label>
                    <flux:input wire:model="regNo" placeholder="AB12 CDE" class="uppercase tracking-wider font-bold" />
                    <flux:error name="regNo" />
                </flux:field>
                <flux:field>
                    <flux:label>Make</flux:label>
                    <flux:input wire:model="make" placeholder="e.g. Honda" />
                </flux:field>
            </x-site.form-grid>

            <flux:field>
                <flux:label>Model</flux:label>
                <flux:input wire:model="model" placeholder="e.g. CBR500R" />
            </flux:field>

            <flux:separator class="my-2 border-slate-200 dark:border-gray-600" />
            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700 dark:text-gray-300">Your details</h3>

            <flux:field>
                <flux:label>Full Name *</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Email *</flux:label>
                    <flux:input wire:model.live="email" type="email" />
                    <flux:error name="email" />
                </flux:field>
                <flux:field>
                    <flux:label>Phone *</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>
            </x-site.form-grid>

            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Preferred Date *</flux:label>
                    <x-site.booking-date-picker wire:model.live="preferredDate" min="{{ \App\Support\BookingSchedule::minBookableDate(true) }}" />
                    <flux:error name="preferredDate" />
                </flux:field>
                <flux:field>
                    <flux:label>Preferred Time *</flux:label>
                    <flux:select wire:model="preferredTime" variant="listbox" placeholder="Select time…">
                        @foreach($this->availableTimeSlots as $val => $label)
                            <flux:select.option value="{{ $val }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="preferredTime" />
                </flux:field>
            </x-site.form-grid>

            <flux:field>
                <flux:label>Additional Notes</flux:label>
                <flux:textarea wire:model="notes" rows="5" />
            </flux:field>

            <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-green text-white hover:bg-brand-green-dark">
                Submit MOT Booking
            </flux:button>
        </form>
    </x-site.form-panel>
