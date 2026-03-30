<div class="min-h-screen bg-gray-100 dark:bg-gray-950">
    <section class="bg-gradient-to-br from-red-800 via-red-700 to-slate-900 text-white border-b border-red-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight">The UK’s Leading Motorcycle Delivery Service</h1>
            <p class="mt-3 text-base md:text-lg font-semibold">Free Collection When You Repair with Us!</p>
            <p class="mt-2 text-slate-100">Call us for a free quote.</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" id="delivery-form">
        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        @if($step === 1)
            <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Step 1: Enter postcodes</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">From Postcode and To Postcode to calculate distance and proceed.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <flux:field>
                        <flux:label>From Postcode</flux:label>
                        <flux:input wire:model.defer="pickupPostcode" class="uppercase" placeholder="ENTER PICKUP POSTAL CODE" />
                        <flux:error name="pickupPostcode" />
                    </flux:field>
                    <flux:field>
                        <flux:label>To Postcode</flux:label>
                        <flux:input wire:model.defer="dropoffPostcode" class="uppercase" placeholder="ENTER DELIVERY POSTAL CODE" />
                        <flux:error name="dropoffPostcode" />
                    </flux:field>
                </div>
                <flux:button wire:click="proceedToStepTwo" variant="filled" class="mt-4 w-full bg-brand-red text-white hover:bg-red-700" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="proceedToStepTwo">Proceed to Next Step</span>
                    <span wire:loading wire:target="proceedToStepTwo">Calculating distance…</span>
                </flux:button>
            </section>
        @endif

        @if($step === 2)
            <form wire:submit="submitOrder" class="space-y-5">
                <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Step 2: Complete your order</h2>
                        <button type="button" wire:click="startOver" class="text-xs text-red-600 hover:underline">Start Over</button>
                    </div>
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-200">
                        Approximate Distance: <strong>{{ number_format($distance, 2) }} miles</strong>
                    </div>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pickup Details</h3>
                        <flux:field><flux:label>Pickup Postcode</flux:label><flux:input wire:model="pickupPostcode" class="uppercase" /><flux:error name="pickupPostcode" /></flux:field>
                        <flux:field><flux:label>Pickup Address</flux:label><flux:input wire:model="pickupAddress" /><flux:error name="pickupAddress" /></flux:field>
                    </section>
                    <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delivery Details</h3>
                        <flux:field><flux:label>Dropoff Postcode</flux:label><flux:input wire:model="dropoffPostcode" class="uppercase" /><flux:error name="dropoffPostcode" /></flux:field>
                        <flux:field><flux:label>Dropoff Address</flux:label><flux:input wire:model="dropoffAddress" /><flux:error name="dropoffAddress" /></flux:field>
                    </section>
                </div>

                <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bike Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Vehicle Pickup Date & Time</flux:label>
                            <flux:input wire:model="pickUpDatetime" type="datetime-local" />
                            <flux:error name="pickUpDatetime" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Vehicle Registration Number</flux:label>
                            <flux:input wire:model="vrm" class="uppercase" />
                            <flux:error name="vrm" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Vehicle Type</flux:label>
                            <flux:select wire:model="vehicleTypeId" variant="listbox">
                                @foreach ($vehicleTypes as $vehicleType)
                                    <flux:select.option value="{{ $vehicleType->id }}">{{ $vehicleType->name }} ({{ $vehicleType->cc_range }} CC)</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="vehicleTypeId" />
                        </flux:field>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-end">
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="moveable"> Is the bike moveable?</label>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="documents"> Are all documents present?</label>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="keys"> Are all keys present?</label>
                        </div>
                    </div>
                </section>

                <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Additional Information</h3>
                    <flux:field>
                        <flux:label>Additional Note</flux:label>
                        <flux:textarea wire:model="note" rows="4" placeholder="Include any additional information such as items received with the bike, etc." />
                        <flux:error name="note" />
                    </flux:field>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Your Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field><flux:label>Full Name</flux:label><flux:input wire:model="fullName" /><flux:error name="fullName" /></flux:field>
                        <flux:field><flux:label>Phone Number</flux:label><flux:input wire:model="phone" /><flux:error name="phone" /></flux:field>
                        <flux:field><flux:label>Email Address *</flux:label><flux:input wire:model="email" type="email" /><flux:error name="email" /></flux:field>
                        <flux:field><flux:label>Address</flux:label><flux:input wire:model="customerAddress" /><flux:error name="customerAddress" /></flux:field>
                    </div>
                    <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300"><input type="checkbox" wire:model="terms" class="mt-1"><span>I confirm all details are correct and authorise this order.</span></label>
                    <flux:error name="terms" />
                </section>

                <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-red-700" wire:loading.attr="disabled">
                    <span wire:loading.remove>Complete Order</span>
                    <span wire:loading>Submitting order…</span>
                </flux:button>
            </form>
        @endif

        <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-2xl font-bold text-brand-red">Vehicle Delivery Service</h2>
            <p class="text-base text-gray-700 dark:text-gray-300 mt-4">NGN Motors specialises in secure, efficient motorcycle transport solutions that safeguard your ride, simplify logistics, and enhance service quality for both private and commercial clients.</p>
            <p class="mt-4 text-gray-900 dark:text-white"><strong>🔧 FREE Collection When You Choose Our Repair Service!</strong></p>
            <p class="mt-2 text-gray-700 dark:text-gray-300">We’ll collect your motorcycle at no extra cost when you book repairs at any of our professional workshops. Save money while getting expert service - let us handle both pickup and repairs for a seamless experience.</p>
            <ul class="mt-4 space-y-2 text-gray-700 dark:text-gray-300">
                <li>🚚 <strong>Nationwide transport services JUST £249.99 anywhere in the England</strong></li>
                <li>🕒 <strong>Serving the industry since 2018 with over 6 years of expertise</strong></li>
                <li>🔒 <strong>Fully insured, safe, and reliable vehicle transport</strong></li>
                <li>💰 <strong>Comprehensive coverage up to £100,000 per vehicle</strong></li>
                <li>🌐 <strong>Offering both local and international transport solutions</strong></li>
                <li>📝 <strong>Vehicle inspections performed at the time of collection</strong></li>
                <li>🖥️ <strong>Convenient self-service admin portal for easy management</strong></li>
            </ul>
        </section>

        <section x-data="{open:null}" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Frequently Asked Questions</h3>
            @php
                $faqs = [
                    ['q' => 'How free collection works?', 'a' => 'We will collect your motorcycle to our workshop and the quotated amount you would require to pay before collection. We will deduct this amount from the total cost of the repair. In case you are not happy to proceed with the repair, the "vehicle collection" amount will not be refunded.'],
                    ['q' => 'Do you collaborate with trade partners for motorcycle deliveries and collections?', 'a' => 'Yes, as a motorcycle transport company, we partner with major brands and dealerships across the UK. For more information about our Trade Motorcycle Deliveries, please click the link to register your details. Our account manager will reach out to discuss your needs.'],
                    ['q' => 'How quickly can you deliver my motorcycle?', 'a' => 'Depending on the motorcycle transport service you request, we offer delivery options ranging from 24-hour next-day delivery to 7 working days.'],
                    ['q' => 'What insurance do you provide to protect my motorcycle during transit?', 'a' => 'Our vans are specifically designed for the safe transportation of motorcycles, and our drivers are trained and experienced in handling them. We understand the importance of having comprehensive insurance for your peace of mind. Our goods in transit and motor insurance covers us up to £100,000, and we are insured with Allianz, one of Europe\'s largest insurers.'],
                    ['q' => 'What type of van will transport my motorcycle?', 'a' => 'All our vans are custom long wheelbase vehicles, specifically modified for the safe transport of motorcycles. We are the only motorcycle transport company in the UK that fully customises all our vans for this purpose.'],
                    ['q' => 'Are your motorcycle delivery drivers also bikers?', 'a' => 'We strive to hire individuals who are passionate about motorcycles and have an engineering background. As a company, we take pride in being part of the UK biking community, and we are dedicated to everything related to two wheels.'],
                    ['q' => 'Are your motorcycle collection drivers qualified mechanics?', 'a' => 'Yes, all our motorcycle breakdown recovery drivers are fully trained in modern mechanics to ensure you get back on the road as quickly as possible.'],
                    ['q' => 'How can I make a payment?', 'a' => 'You can pay for your motorcycle collection or delivery either by booking online through our website or by calling one of our advisers over the phone.'],
                    ['q' => 'Can I pay in cash upon collection?', 'a' => 'No, we have a no-cash policy to ensure the safety of our drivers. All motorcycle collections, deliveries, and breakdowns must be paid in advance, either through our website or over the phone with one of our advisers.'],
                    ['q' => 'What types of motorcycles can you transport?', 'a' => 'We are capable of transporting all types of motorcycles, from 125cc models to the largest bikes, including Harley Davidsons and Triumph Rockets. No motorcycle transport job is too big or too small for us.'],
                ];
            @endphp
            <div class="space-y-2">
                @foreach($faqs as $i => $faq)
                    <div class="border border-gray-200 dark:border-gray-700">
                        <button type="button" class="w-full text-left px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center justify-between" @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <span>{{ $faq['q'] }}</span>
                            <span x-text="open === {{ $i }} ? '-' : '+'"></span>
                        </button>
                        <div x-show="open === {{ $i }}" class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300">{{ $faq['a'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
