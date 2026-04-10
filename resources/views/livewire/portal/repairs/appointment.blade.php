<div>
    <flux:heading size="xl" class="mb-2">Repairs appointment</flux:heading>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
        Book a dated workshop slot. This creates a record in customer appointments and emails you and our service team.
        For a general enquiry only (no slot), use <a href="{{ route('account.repairs.request') }}" class="text-brand-red font-medium underline decoration-brand-red/80 hover:text-brand-red-dark">Repair enquiry</a>.
    </p>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:callout variant="info" icon="information-circle" class="mb-6">
        <flux:callout.text>We will confirm your date and time. If you need a quick question answered first, send a repair enquiry instead.</flux:callout.text>
    </flux:callout>

    <form wire:submit.prevent="submit" class="space-y-6">
        <flux:card class="p-6 space-y-6">
            <flux:field>
                <flux:label>Service type *</flux:label>
                <flux:select wire:model="service_type" variant="listbox" placeholder="Select service type">
                    <flux:select.option value="basic_service">Basic service</flux:select.option>
                    <flux:select.option value="full_service">Full / major service</flux:select.option>
                    <flux:select.option value="repairs">Repairs / diagnosis</flux:select.option>
                    <flux:select.option value="tyres">Tyres</flux:select.option>
                    <flux:select.option value="brakes">Brakes</flux:select.option>
                    <flux:select.option value="electrical">Electrical</flux:select.option>
                    <flux:select.option value="other">Other</flux:select.option>
                </flux:select>
                <flux:error name="service_type" />
            </flux:field>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Bike details</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Registration number *</flux:label>
                            <flux:input wire:model="bike_reg_no" type="text" placeholder="AB12 CDE" class="uppercase" />
                            <flux:error name="bike_reg_no" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>Make</flux:label>
                        <flux:input wire:model="bike_make" type="text" placeholder="Honda" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Model</flux:label>
                        <flux:input wire:model="bike_model" type="text" placeholder="PCX 125" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:label>Current mileage</flux:label>
                    <flux:input wire:model="mileage" type="number" placeholder="12000" />
                </flux:field>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Describe the issue</flux:label>
                <flux:textarea wire:model="issue_description" rows="4" placeholder="Describe the problem, when it happens, what you have tried…" />
            </flux:field>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Preferred slot</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Date *</flux:label>
                        <flux:date-picker wire:model="date_requested" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                        <flux:error name="date_requested" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Time *</flux:label>
                        <flux:select wire:model="time_slot" variant="listbox" placeholder="Select time">
                            @foreach($timeSlots as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="time_slot" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Branch *</flux:label>
                        <flux:select wire:model="branch_id" variant="listbox" searchable placeholder="Select branch">
                            @foreach($branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Approve repairs up to *</flux:label>
                <flux:select wire:model="repair_authorisation_limit" variant="listbox" placeholder="Select authorisation limit">
                    <flux:select.option value="0">Do not repair without approval</flux:select.option>
                    <flux:select.option value="50">Up to £50</flux:select.option>
                    <flux:select.option value="100">Up to £100</flux:select.option>
                    <flux:select.option value="150">Up to £150</flux:select.option>
                    <flux:select.option value="200">Up to £200</flux:select.option>
                    <flux:select.option value="999999">Any amount needed</flux:select.option>
                </flux:select>
                <flux:error name="repair_authorisation_limit" />
            </flux:field>

            <div class="flex justify-between items-center pt-2">
                <flux:button href="{{ route('account.bookings') }}" variant="outline">Cancel</flux:button>
                <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                    <span wire:loading.remove>Request appointment</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
