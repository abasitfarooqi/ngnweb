<div>
<div class="bg-gray-900 text-white py-14">
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

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-8">
            <form wire:submit="submitRequest" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                </div>
                <flux:field>
                    <flux:label>Pickup Address *</flux:label>
                    <flux:input wire:model="fromAddress" placeholder="Street, area or postcode" />
                    <flux:error name="fromAddress" />
                </flux:field>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Destination Branch *</flux:label>
                        <flux:select wire:model.live="branchId" variant="listbox" searchable placeholder="Choose branch">
                            @foreach($branches as $branch)
                                <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>Destination Address *</flux:label>
                        <flux:input wire:model="toAddress" placeholder="Branch address" />
                        <flux:error name="toAddress" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:label>Describe the Problem</flux:label>
                    <flux:textarea wire:model="message" rows="3" />
                </flux:field>
                <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" wire:model="terms" class="mt-1">
                    <span>I agree to the recovery terms and confirm I am authorised for this motorcycle.</span>
                </label>
                <flux:error name="terms" />
                <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                    Submit Recovery Request
                </flux:button>
                <p class="text-xs text-gray-500 text-center">For emergencies call us directly: <a href="tel:02083141498" class="text-brand-red hover:underline">0208 314 1498</a></p>
            </form>
        </flux:card>
    </div>
</div>
</div>
