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

    @if($step === 1)
        <flux:card class="p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Step 1: Postcode A and B</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Postcode A (Pickup)</flux:label>
                    <flux:input wire:model.defer="pickupPostcode" class="uppercase" />
                    <flux:error name="pickupPostcode" />
                </flux:field>
                <flux:field>
                    <flux:label>Postcode B (Dropoff)</flux:label>
                    <flux:input wire:model.defer="dropoffPostcode" class="uppercase" />
                    <flux:error name="dropoffPostcode" />
                </flux:field>
            </div>
            <flux:button wire:click="proceedToStepTwo" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="proceedToStepTwo">Calculate Distance & Continue</span>
                <span wire:loading wire:target="proceedToStepTwo">Calculating…</span>
            </flux:button>
        </flux:card>
    @endif

    @if($step === 2)
        <form wire:submit.prevent="submitOrder" class="space-y-6">
            <flux:card class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Step 2: Complete Recovery Order</h3>
                    <button type="button" wire:click="startOver" class="text-xs text-red-600 hover:underline">Start Over</button>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Calculated distance: <strong>{{ number_format($distance, 2) }} miles</strong></p>
            </flux:card>

            <flux:card class="p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Route Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field><flux:label>Pickup Postcode</flux:label><flux:input wire:model="pickupPostcode" class="uppercase" /><flux:error name="pickupPostcode" /></flux:field>
                    <flux:field><flux:label>Dropoff Postcode</flux:label><flux:input wire:model="dropoffPostcode" class="uppercase" /><flux:error name="dropoffPostcode" /></flux:field>
                    <flux:field><flux:label>Pickup Address</flux:label><flux:input wire:model="pickupAddress" /><flux:error name="pickupAddress" /></flux:field>
                    <flux:field><flux:label>Dropoff Address</flux:label><flux:input wire:model="dropoffAddress" /><flux:error name="dropoffAddress" /></flux:field>
                </div>
            </flux:card>

            <flux:card class="p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bike and Pickup</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field><flux:label>Pickup Date & Time</flux:label><flux:input wire:model="pickUpDatetime" type="datetime-local" /><flux:error name="pickUpDatetime" /></flux:field>
                    <flux:field><flux:label>Vehicle Registration</flux:label><flux:input wire:model="vrm" class="uppercase" /><flux:error name="vrm" /></flux:field>
                    <flux:field>
                        <flux:label>Vehicle Type</flux:label>
                        <flux:select wire:model="vehicleTypeId" variant="listbox">
                            @foreach($vehicleTypes as $vehicleType)
                                <flux:select.option value="{{ $vehicleType->id }}">{{ $vehicleType->name }} ({{ $vehicleType->cc_range }} CC)</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="vehicleTypeId" />
                    </flux:field>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-end">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="moveable"> Moveable</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="documents"> Documents</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="keys"> Keys</label>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Customer Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field><flux:label>Full Name</flux:label><flux:input wire:model="fullName" /><flux:error name="fullName" /></flux:field>
                    <flux:field><flux:label>Phone</flux:label><flux:input wire:model="phone" /><flux:error name="phone" /></flux:field>
                    <flux:field><flux:label>Email</flux:label><flux:input wire:model="email" type="email" /><flux:error name="email" /></flux:field>
                    <flux:field><flux:label>Address</flux:label><flux:input wire:model="customerAddress" /><flux:error name="customerAddress" /></flux:field>
                    <flux:field class="md:col-span-2"><flux:label>Additional Note</flux:label><flux:textarea wire:model="note" rows="4" /><flux:error name="note" /></flux:field>
                </div>
                <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300"><input type="checkbox" wire:model="terms" class="mt-1"><span>I confirm these details are correct.</span></label>
                <flux:error name="terms" />
                <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                    <span wire:loading.remove>Complete Order</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>
            </flux:card>
        </form>
    @endif
</div>
