<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Contact Us</h1>
        <p class="text-gray-300 text-lg">Get in touch with our team – we're here to help</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- Contact form --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Send Us a Message</h2>

            @if(session()->has('success'))
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    <flux:callout.text>{{ session('success') }}</flux:callout.text>
                </flux:callout>
            @endif

            <form wire:submit.prevent="submit" class="space-y-4">
                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                </div>

                <flux:field>
                    <flux:label>Preferred Branch</flux:label>
                    <flux:select wire:model="branch_id" variant="listbox" searchable placeholder="Any branch">
                        <flux:select.option value="">Any branch</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="branch_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Topic *</flux:label>
                    <flux:select wire:model="topic" variant="listbox" placeholder="Select a topic">
                        @foreach(['rentals'=>'Rentals','mot'=>'MOT','repairs'=>'Repairs & Servicing','sales'=>'Bike Sales','finance'=>'Finance','recovery'=>'Recovery','other'=>'Other'] as $val => $label)
                            <flux:select.option value="{{ $val }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="topic" />
                </flux:field>

                <flux:field>
                    <flux:label>Message *</flux:label>
                    <flux:textarea wire:model="message" rows="5" />
                    <flux:error name="message" />
                </flux:field>

                <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                    Send Message
                </flux:button>
            </form>

            {{-- Sub-contact forms tabs --}}
            <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Specific Enquiries</h3>
                <div class="flex flex-wrap gap-3">
                    <flux:button href="/contact/service-booking" variant="outline" size="sm">Book a Service</flux:button>
                    <flux:button href="/contact/call-back" variant="outline" size="sm">Request a Call Back</flux:button>
                    <flux:button href="/contact/trade-account" variant="outline" size="sm">Trade Account</flux:button>
                </div>
            </div>
        </div>

        {{-- Branches --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Visit Us</h2>
            <div class="space-y-5">
                @foreach($branches as $branch)
                    @php
                        $phone   = $branch->phone    ?? config('site.branches.' . strtolower($branch->name) . '.phone');
                        $address = $branch->address  ?? config('site.branches.' . strtolower($branch->name) . '.address');
                        $postcode= $branch->postal_code ?? '';
                    @endphp
                    <flux:card class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $branch->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $address }} {{ $postcode }}</p>
                        <div class="flex gap-3 flex-wrap">
                            <a href="tel:{{ $phone }}" class="flex items-center gap-1.5 text-sm text-brand-red font-medium hover:underline">
                                <flux:icon name="phone" class="h-4 w-4" /> {{ $phone }}
                            </a>
                            <a href="mailto:enquiries@neguinhomotors.co.uk" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-red hover:underline">
                                <flux:icon name="envelope" class="h-4 w-4" /> Email
                            </a>
                        </div>
                    </flux:card>
                @endforeach

                {{-- WhatsApp --}}
                <flux:card class="p-5 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">WhatsApp Us</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Get a quick response on WhatsApp</p>
                    <flux:button href="https://wa.me/447951790568?text=Hello%20NGN" target="_blank" variant="filled" size="sm" class="bg-green-600 text-white hover:bg-green-700">
                        Open WhatsApp
                    </flux:button>
                </flux:card>
            </div>
        </div>
    </div>
</div>
</div>
