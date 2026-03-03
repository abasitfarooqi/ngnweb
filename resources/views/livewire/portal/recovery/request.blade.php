<div>
    <flux:heading size="xl" class="mb-6">Request Recovery Service</flux:heading>

    @if (session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Urgent CTA --}}
    <flux:callout variant="danger" icon="exclamation-triangle" class="mb-6">
        <flux:callout.heading>For Urgent Recovery — Call Now</flux:callout.heading>
        <flux:callout.text>
            <strong>Catford:</strong> <a href="tel:02083141498" class="underline">0208 314 1498</a> &nbsp;
            <strong>Tooting:</strong> <a href="tel:02034095478" class="underline">0203 409 5478</a> &nbsp;
            <strong>Sutton:</strong> <a href="tel:02084129275" class="underline">0208 412 9275</a>
        </flux:callout.text>
    </flux:callout>

    <form wire:submit.prevent="submit" class="space-y-6">
        <flux:card class="p-6 space-y-6">
            <div>
                <flux:label class="mb-2">Urgency *</flux:label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="urgency" value="urgent" class="text-brand-red">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Urgent (I'm stuck now)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="urgency" value="planned" class="text-brand-red">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Planned (collect for repairs)</span>
                    </label>
                </div>
                <flux:error name="urgency" />
            </div>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Pickup Location</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Postcode *</flux:label>
                        <flux:input wire:model="pickup_postcode" type="text" placeholder="SE6 4NU" class="uppercase" />
                        <flux:error name="pickup_postcode" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Full Address/Location *</flux:label>
                        <flux:input wire:model="pickup_location" type="text" placeholder="Street name, landmarks" />
                        <flux:error name="pickup_location" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Bike Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Registration Number</flux:label>
                        <flux:input wire:model="bike_reg_no" type="text" placeholder="AB12 CDE" class="uppercase" />
                        <flux:error name="bike_reg_no" />
                    </flux:field>
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
            </div>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">What's Wrong?</h3>
                <flux:field class="mb-4">
                    <flux:label>Issue Type *</flux:label>
                    <flux:select wire:model="issue_type" variant="listbox" placeholder="Select issue type">
                        <flux:select.option value="wont_start">Won't Start</flux:select.option>
                        <flux:select.option value="flat_tyre">Flat Tyre</flux:select.option>
                        <flux:select.option value="electrical">Electrical Problem</flux:select.option>
                        <flux:select.option value="accident">Accident</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    <flux:error name="issue_type" />
                </flux:field>
                <flux:field>
                    <flux:label>Additional Details</flux:label>
                    <flux:textarea wire:model="issue_description" rows="4" placeholder="Describe the problem..." />
                    <flux:error name="issue_description" />
                </flux:field>
            </div>

            <flux:separator />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Destination</h3>
                <flux:field class="mb-4">
                    <flux:label>Deliver To *</flux:label>
                    <flux:select wire:model="destination_type" variant="listbox" placeholder="Select destination">
                        <flux:select.option value="branch">NGN Branch</flux:select.option>
                        <flux:select.option value="home">My Home</flux:select.option>
                        <flux:select.option value="other">Other Address</flux:select.option>
                    </flux:select>
                    <flux:error name="destination_type" />
                </flux:field>
                @if($destination_type === 'branch')
                    <flux:field>
                        <flux:label>Select Branch</flux:label>
                        <flux:select wire:model="branch_id" variant="listbox" searchable placeholder="Choose branch">
                            @foreach($branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>
                @elseif($destination_type === 'other')
                    <flux:field>
                        <flux:label>Destination Address</flux:label>
                        <flux:input wire:model="destination_address" type="text" placeholder="Full address" />
                        <flux:error name="destination_address" />
                    </flux:field>
                @endif
            </div>

            <flux:separator />

            <div class="flex justify-between items-center">
                <flux:button href="{{ route('account.dashboard') }}" variant="outline">Cancel</flux:button>
                <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit Request</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
