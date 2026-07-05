<div>
<div class="site-page-hero py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycle Delivery & Recovery</h1>
        <p class="text-gray-300 text-lg mb-6">24/7 breakdown assistance across London & surrounding areas</p>
        <flux:button href="tel:02083141498" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark text-lg px-8 py-3">
            Call Now: 0208 314 1498
        </flux:button>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
        @foreach([
            ['icon'=>'bolt', 'title'=>'24/7 Recovery', 'text'=>'Round-the-clock motorcycle recovery across London. Broken down? We\'ll get you moving.'],
            ['icon'=>'truck', 'title'=>'Delivery Service', 'text'=>'Motorbike delivery & collection service. We bring your rental to you or collect for repairs.'],
            ['icon'=>'shield-check', 'title'=>'Safe & Secure', 'text'=>'Specialist motorcycle transport equipment. Your bike arrives safely.'],
        ] as $item)
            <flux:card class="p-6 text-center">
                <div class="w-14 h-14 bg-brand-red flex items-center justify-center mx-auto mb-4">
                    <flux:icon name="{{ $item['icon'] }}" class="h-7 w-7 text-white" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item['text'] }}</p>
            </flux:card>
        @endforeach
    </div>

    {{-- Request form --}}
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Request Recovery</h2>

        <x-site.form-panel>
            @if(session('success'))
                <flux:callout variant="success" icon="check-circle" class="mb-5">
                    <flux:callout.text>{{ session('success') }}</flux:callout.text>
                </flux:callout>
            @endif

            <form wire:submit="submitRequest" class="site-form site-form-stack">
                <x-site.form-grid :cols="2">
                    <flux:field>
                        <flux:label>Your Name *</flux:label>
                        <flux:input wire:model="name" />
                        <flux:error name="name" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Email *</flux:label>
                        <flux:input wire:model="email" type="email" />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone *</flux:label>
                        <flux:input wire:model="phone" type="tel" />
                        <flux:error name="phone" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Bike Registration *</flux:label>
                        <flux:input wire:model="bikeReg" placeholder="AB12 CDE" class="uppercase" />
                        <flux:error name="bikeReg" />
                    </flux:field>
                </x-site.form-grid>
                <flux:field>
                    <flux:label>Pickup Address *</flux:label>
                    <flux:input wire:model="fromAddress" placeholder="Street, area or postcode" />
                    <flux:error name="fromAddress" />
                </flux:field>
                <x-site.form-grid :cols="2">
                    <flux:field>
                        <flux:label>Destination Branch (optional)</flux:label>
                        <flux:select wire:model.live="branchId" variant="listbox" searchable placeholder="— or enter address below —">
                            <flux:select.option value="">Custom address</flux:select.option>
                            @foreach($branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:description>Select a branch or leave blank and type a custom address.</flux:description>
                    </flux:field>
                    <flux:field>
                        <flux:label>Destination Address *</flux:label>
                        <flux:input wire:model="toAddress" placeholder="Branch or any address" />
                        <flux:error name="toAddress" />
                    </flux:field>
                </x-site.form-grid>
                <flux:field>
                    <flux:label>Describe the Problem</flux:label>
                    <flux:textarea wire:model="message" rows="3" />
                </flux:field>
                {{-- Terms & Conditions --}}
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Terms & Conditions</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>The recovery service is subject to availability and location.</li>
                        <li>Payment must be made before or upon collection of the vehicle.</li>
                        <li>We reserve the right to refuse service if the vehicle condition poses safety risks.</li>
                        <li>You confirm that you are the legal owner or authorised representative for the vehicle.</li>
                        <li>NGN is not liable for any pre-existing damage to the vehicle.</li>
                        <li>Personal information will be handled in accordance with our privacy policy.</li>
                        <li>Changes to terms may occur with notice.</li>
                    </ul>
                </div>

                <label class="site-form-consent">
                    <input type="checkbox" wire:model="terms">
                    <span>I agree to the recovery terms and conditions above, and confirm I am authorised for this motorcycle.</span>
                </label>
                <flux:error name="terms" />
                <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-green text-white hover:bg-brand-green-dark">
                    Submit Recovery Request
                </flux:button>
                <p class="text-xs text-gray-500 text-center">For emergencies call us directly: <a href="tel:02083141498" class="text-brand-green hover:underline">0208 314 1498</a></p>
            </form>
        </x-site.form-panel>
    </div>
</div>
</div>
