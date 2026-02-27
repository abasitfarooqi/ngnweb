<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Trade Account Application</h1>
        <p class="text-xl text-red-100">Exclusive benefits for trade customers & businesses</p>
    </div>
</div>

{{-- Benefits --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        @foreach([
            ['icon' => 'banknotes',    'title' => 'Exclusive Discounts', 'desc' => 'Special trade pricing on parts, services & accessories'],
            ['icon' => 'document-text','title' => 'Credit Terms',        'desc' => 'Flexible payment terms for approved businesses'],
            ['icon' => 'truck',        'title' => 'Priority Service',    'desc' => 'Fast-track orders & dedicated account manager'],
        ] as $benefit)
            <flux:card class="p-8 text-center">
                <flux:icon name="{{ $benefit['icon'] }}" class="h-10 w-10 text-brand-red mx-auto mb-4" />
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $benefit['title'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $benefit['desc'] }}</p>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Application Form --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:card class="p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Trade Account Application Form</h2>

        <form wire:submit="submitEnquiry" class="space-y-5">
            <flux:field>
                <flux:label>Company Name *</flux:label>
                <flux:input wire:model="companyName" type="text" placeholder="Your company name" />
                <flux:error name="companyName" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Contact Name *</flux:label>
                    <flux:input wire:model="contactName" type="text" />
                    <flux:error name="contactName" />
                </flux:field>
                <flux:field>
                    <flux:label>Email *</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Phone *</flux:label>
                <flux:input wire:model="phone" type="tel" />
                <flux:error name="phone" />
            </flux:field>

            <flux:field>
                <flux:label>Business Address *</flux:label>
                <flux:textarea wire:model="address" rows="3" />
                <flux:error name="address" />
            </flux:field>

            <flux:field>
                <flux:label>VAT Number (Optional)</flux:label>
                <flux:input wire:model="vatNumber" type="text" placeholder="GB123456789" />
            </flux:field>

            <flux:field>
                <flux:label>Tell Us About Your Business *</flux:label>
                <flux:textarea wire:model="message" rows="4" placeholder="What type of business do you operate? What products/services are you interested in?" />
                <flux:error name="message" />
            </flux:field>

            <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">Submit Application</flux:button>
        </form>
    </flux:card>
</div>
</div>
