<div>
@if(! $embedded)
{{-- Hero --}}
<div class="site-page-hero bg-gradient-to-r from-brand-green to-emerald-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Book a Service</h1>
        <p class="text-xl text-emerald-100">Fast, convenient online service booking</p>
    </div>
</div>
@endif

{{-- Form --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 {{ $embedded ? 'py-0' : 'py-12' }}">
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if($this->serviceType === 'MOT Booking Enquiry' && $this->activeCustomerBooking)
        <flux:callout variant="warning" icon="information-circle" class="mb-6">
            <flux:callout.text>
                We already have a MOT booking for {{ $this->activeCustomerBooking['registration'] }} on {{ $this->activeCustomerBooking['date'] }} at {{ $this->activeCustomerBooking['time'] }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    <x-site.form-panel :title="$embeddedHeading ?: ($embedded ? 'Service enquiry' : 'Service Booking Form')">
        <form wire:key="service-booking-form-{{ $formNonce }}" wire:submit.prevent="submitBooking" class="site-form site-form-stack">
            @if($portalRepairsEnquiry && $repairsEnquiryCompactMode)
                <flux:field>
                    <flux:label>Enquiry type *</flux:label>
                    <flux:select wire:model.live="serviceType" variant="listbox" placeholder="Select…">
                        @foreach (\App\Livewire\Site\Contact\ServiceBooking::portalRepairsServiceTypeOptions() as $svcValue => $svcLabel)
                            <flux:select.option value="{{ $svcValue }}">{{ $svcLabel }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="serviceType" />
                </flux:field>
                <x-site.form-grid :cols="3">
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
                </x-site.form-grid>
            @endif

            @if(! $rentalCompactMode && ! $repairsEnquiryCompactMode)
                <flux:field>
                    <flux:label>Service Type *</flux:label>
                    <flux:select wire:model.live="serviceType" variant="listbox" placeholder="Select service...">
                        @foreach (\App\Livewire\Site\Contact\ServiceBooking::publicServiceTypeOptions() as $serviceOption)
                            <flux:select.option value="{{ $serviceOption }}">{{ $serviceOption }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="serviceType" />
                </flux:field>
            @endif

            @if(! $rentalCompactMode && ! $repairsEnquiryCompactMode)
                @if($serviceType === 'MOT Booking Enquiry')
                    <flux:field>
                        <flux:label>Branch</flux:label>
                        <flux:input value="Catford" disabled />
                        <flux:error name="selectedBranch" />
                    </flux:field>
                @else
                    <flux:field>
                        <flux:label>Select Branch</flux:label>
                        <flux:select wire:model="selectedBranch" variant="listbox" searchable placeholder="Choose a branch if preferred...">
                            @foreach($branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="selectedBranch" />
                    </flux:field>
                @endif
            @endif

            @if(! $rentalCompactMode && ! $repairsEnquiryCompactMode)
                <x-site.form-grid :cols="3">
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
                </x-site.form-grid>
            @endif

            @if (! ($portalRepairsEnquiry && $repairsEnquiryCompactMode))
                <x-site.form-grid :cols="2">
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
                </x-site.form-grid>
            @endif

            <flux:field>
                <flux:label>Email @if($portalRepairsEnquiry && $repairsEnquiryCompactMode) * @endif</flux:label>
                <flux:input wire:model="email" type="email" />
                <flux:error name="email" />
            </flux:field>

            @if($this->requiresScheduleSelection)
                <x-site.form-grid :cols="2">
                    <flux:field>
                        <flux:label>Preferred Date *</flux:label>
                        <x-site.booking-date-picker wire:model.live="preferredDate" />
                        <flux:error name="preferredDate" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Preferred Time *</flux:label>
                        <flux:select wire:model="preferredTime" variant="listbox" placeholder="Select time...">
                            @foreach($this->availableTimeSlots as $timeValue => $timeLabel)
                                <flux:select.option value="{{ $timeValue }}">{{ $timeLabel }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="preferredTime" />
                    </flux:field>
                </x-site.form-grid>
            @endif

            <flux:field>
                <flux:label>{{ $notesLabel }}</flux:label>
                <flux:textarea wire:model="message" rows="5" placeholder="Any specific issues or requirements?" />
            </flux:field>

            @if(! $rentalCompactMode && ! $repairsEnquiryCompactMode)
                <div>
                    <label class="site-form-consent cursor-pointer">
                        <input type="checkbox" wire:model="cookiePolicy">
                        <span>I have read and agree to the
                            <a href="{{ route('site.privacy') }}" class="text-brand-green font-medium underline decoration-brand-green/80 hover:text-brand-green-dark hover:decoration-brand-green-dark">Cookie and Privacy Policy</a>.
                        </span>
                    </label>
                    <flux:error name="cookiePolicy" />
                </div>
            @endif

            <flux:button type="submit" variant="filled" class="w-full bg-brand-green text-white hover:bg-brand-green-dark" wire:loading.attr="disabled" wire:target="submitBooking">
                <span wire:loading.remove wire:target="submitBooking">{{ $submitLabel }}</span>
                <span wire:loading wire:target="submitBooking">Submitting...</span>
            </flux:button>
        </form>
    </x-site.form-panel>
