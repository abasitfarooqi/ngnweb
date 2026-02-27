<div>
    <flux:heading size="xl" class="mb-6">Book Service or Repairs</flux:heading>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:callout variant="info" icon="information-circle" class="mb-6">
        <flux:callout.heading>Free collection with repairs</flux:callout.heading>
        <flux:callout.text>We offer free collection when you book repairs. Select the collection option below.</flux:callout.text>
    </flux:callout>

    <form wire:submit.prevent="submit" class="space-y-6">
        <flux:card class="p-6 space-y-6">

            <flux:field>
                <flux:label>Service Type *</flux:label>
                <flux:select wire:model="service_type">
                    <option value="basic_service">Basic Service</option>
                    <option value="full_service">Full/Major Service</option>
                    <option value="repairs">Repairs/Diagnosis</option>
                    <option value="tyres">Tyres</option>
                    <option value="brakes">Brakes</option>
                    <option value="electrical">Electrical</option>
                    <option value="other">Other</option>
                </flux:select>
                <flux:error name="service_type" />
            </flux:field>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Bike Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Registration Number *</flux:label>
                            <flux:input wire:model="bike_reg_no" type="text" placeholder="AB12 CDE" class="uppercase" />
                            <flux:error name="bike_reg_no" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>Make</flux:label>
                        <flux:input wire:model="bike_make" type="text" placeholder="Honda" />
                        <flux:error name="bike_make" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Model</flux:label>
                        <flux:input wire:model="bike_model" type="text" placeholder="PCX 125" />
                        <flux:error name="bike_model" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:label>Current Mileage</flux:label>
                    <flux:input wire:model="mileage" type="number" placeholder="12000" />
                    <flux:error name="mileage" />
                </flux:field>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Describe the Issue</flux:label>
                <flux:textarea wire:model="issue_description" rows="4" placeholder="Describe the problem, when it happens, what you've tried..." />
                <flux:error name="issue_description" />
            </flux:field>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Collection (free with repairs)</h3>
                <label class="flex items-center gap-2 cursor-pointer mb-4">
                    <input type="checkbox" wire:model="needs_collection" class="text-brand-red">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I need free collection from my location</span>
                </label>
                @if($needs_collection)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Collection Postcode *</flux:label>
                            <flux:input wire:model="collection_postcode" type="text" placeholder="SE6 4NU" class="uppercase" />
                            <flux:error name="collection_postcode" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Full Address *</flux:label>
                            <flux:input wire:model="collection_address" type="text" placeholder="Street name, building number" />
                            <flux:error name="collection_address" />
                        </flux:field>
                    </div>
                @endif
            </div>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Booking Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Date *</flux:label>
                        <flux:input wire:model="date_requested" type="date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                        <flux:error name="date_requested" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Time Slot *</flux:label>
                        <flux:select wire:model="time_slot">
                            @foreach($timeSlots as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="time_slot" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Branch *</flux:label>
                        <flux:select wire:model="branch_id">
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Approve repairs up to *</flux:label>
                <flux:select wire:model="repair_authorisation_limit">
                    <option value="0">Do not repair without approval</option>
                    <option value="50">Up to £50</option>
                    <option value="100">Up to £100</option>
                    <option value="150">Up to £150</option>
                    <option value="200">Up to £200</option>
                    <option value="999999">Any amount needed</option>
                </flux:select>
                <flux:error name="repair_authorisation_limit" />
            </flux:field>

            <div class="flex justify-between items-center pt-2">
                <flux:button href="{{ route('account.bookings') }}" variant="outline">Cancel</flux:button>
                <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit Booking</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
