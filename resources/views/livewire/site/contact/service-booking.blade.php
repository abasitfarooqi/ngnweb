<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Book a Service</h1>
        <p class="text-xl text-red-100">Fast, convenient online service booking</p>
    </div>
</div>

{{-- Form --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:card class="p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Service Booking Form</h2>

        <form wire:submit="submitBooking" class="space-y-5">
            <flux:field>
                <flux:label>Service Type *</flux:label>
                <flux:select wire:model="serviceType" variant="listbox" placeholder="Select service...">
                    <flux:select.option value="Accident Management Services Enquiry">Accident Management Services Enquiry</flux:select.option>
                    <flux:select.option value="MOT Booking Enquiry">MOT Booking Enquiry</flux:select.option>
                    <flux:select.option value="Motorcycle Repairs">Motorcycle Repairs</flux:select.option>
                    <flux:select.option value="Motorcycle Full Service">Motorcycle Full Service</flux:select.option>
                    <flux:select.option value="Motorcycle Basic Service">Motorcycle Basic Service</flux:select.option>
                    <flux:select.option value="Other">Other</flux:select.option>
                </flux:select>
                <flux:error name="serviceType" />
            </flux:field>

            <flux:field>
                <flux:label>Select Branch</flux:label>
                <flux:select wire:model="selectedBranch" variant="listbox" searchable placeholder="Choose a branch if preferred...">
                    @foreach($branches as $branch)
                        <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="selectedBranch" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Registration</flux:label>
                    <flux:input wire:model="regNo" type="text" placeholder="AB12CDE" class="uppercase" />
                </flux:field>
                <flux:field>
                    <flux:label>Make</flux:label>
                    <flux:input wire:model="make" type="text" placeholder="e.g. Honda" />
                </flux:field>
                <flux:field>
                    <flux:label>Model</flux:label>
                    <flux:input wire:model="model" type="text" placeholder="e.g. CBR500R" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input wire:model="name" type="text" />
                    <flux:error name="name" />
                </flux:field>
                <flux:field>
                    <flux:label>Phone *</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" />
                <flux:error name="email" />
            </flux:field>

            @if($this->requiresScheduleSelection)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Preferred Date *</flux:label>
                        <flux:date-picker wire:model="preferredDate" min="{{ date('Y-m-d') }}" />
                        <flux:error name="preferredDate" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Preferred Time *</flux:label>
                        <flux:select wire:model="preferredTime" variant="listbox" placeholder="Select time...">
                            <flux:select.option value="10:00">10:00</flux:select.option>
                            <flux:select.option value="10:30">10:30</flux:select.option>
                            <flux:select.option value="11:00">11:00</flux:select.option>
                            <flux:select.option value="11:30">11:30</flux:select.option>
                            <flux:select.option value="12:00">12:00</flux:select.option>
                            <flux:select.option value="12:30">12:30</flux:select.option>
                            <flux:select.option value="13:00">13:00</flux:select.option>
                            <flux:select.option value="13:30">13:30</flux:select.option>
                            <flux:select.option value="14:00">14:00</flux:select.option>
                            <flux:select.option value="14:30">14:30</flux:select.option>
                            <flux:select.option value="15:00">15:00</flux:select.option>
                            <flux:select.option value="15:30">15:30</flux:select.option>
                            <flux:select.option value="16:00">16:00</flux:select.option>
                            <flux:select.option value="16:30">16:30</flux:select.option>
                            <flux:select.option value="17:00">17:00</flux:select.option>
                        </flux:select>
                        <flux:error name="preferredTime" />
                    </flux:field>
                </div>
            @endif

            <flux:field>
                <flux:label>Additional Notes</flux:label>
                <flux:textarea wire:model="message" rows="3" placeholder="Any specific issues or requirements?" />
            </flux:field>

            <div class="text-sm text-gray-700 dark:text-gray-300">
                <label class="inline-flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="cookiePolicy" class="mt-1 accent-brand-red">
                    <span>I have read and agree to the Cookie and Privacy Policy.</span>
                </label>
                <flux:error name="cookiePolicy" />
            </div>

            <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled" wire:target="submitBooking">
                <span wire:loading.remove wire:target="submitBooking">Book Service</span>
                <span wire:loading wire:target="submitBooking">Submitting...</span>
            </flux:button>
        </form>
    </flux:card>
</div>
</div>
