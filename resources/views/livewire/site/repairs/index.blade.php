<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycle Repairs & Servicing</h1>
        <p class="text-gray-300 text-lg mb-6">Expert mechanics at Catford, Tooting & Sutton</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Book a Service
        </flux:button>
    </div>
</div>

{{-- Service packages --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Our Service Packages</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        @foreach([
            ['title'=>'Basic Service', 'price'=>'From £79', 'href'=>'/repairs/basic', 'color'=>'border-gray-300', 'items'=>[
                'Engine oil & filter change', 'Brake fluid check', 'Chain & sprocket inspection',
                'Tyre pressure & condition', 'Battery check', 'Safety inspection report',
            ]],
            ['title'=>'Major Service', 'price'=>'From £149', 'href'=>'/repairs/full', 'color'=>'border-brand-red', 'featured'=>true, 'items'=>[
                'Everything in Basic Service', 'Air filter replacement', 'Spark plug replacement',
                'Coolant check/top-up', 'Full brake inspection', 'Fork oil check', 'Road test',
            ]],
            ['title'=>'Repairs & Diagnostics', 'price'=>'From £49', 'href'=>'/repairs/repair-services', 'color'=>'border-gray-300', 'items'=>[
                'Engine diagnostics', 'Clutch repair/replacement', 'Brake pad replacement',
                'Tyre fitting', 'Suspension service', 'Electrical diagnostics',
            ]],
        ] as $pkg)
            <flux:card class="p-6 border-t-4 {{ $pkg['color'] }} relative">
                @if(isset($pkg['featured']))
                    <flux:badge color="red" class="absolute -top-3 right-4 text-xs font-bold">Most Popular</flux:badge>
                @endif
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $pkg['title'] }}</h3>
                <p class="text-brand-red font-bold text-2xl mb-4">{{ $pkg['price'] }}</p>
                <ul class="space-y-2 mb-6">
                    @foreach($pkg['items'] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" />{{ $item }}
                        </li>
                    @endforeach
                </ul>
                <flux:button href="{{ $pkg['href'] }}" variant="{{ isset($pkg['featured']) ? 'filled' : 'outline' }}" class="w-full {{ isset($pkg['featured']) ? 'bg-brand-red text-white' : '' }}">
                    Learn More
                </flux:button>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Why NGN --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Why Choose NGN?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            @foreach([
                ['icon'=>'check-badge', 'title'=>'Experienced Mechanics', 'text'=>'Our team has years of experience with all makes and models.'],
                ['icon'=>'clock', 'title'=>'Fast Turnaround', 'text'=>'Most services completed same day. No long waits.'],
                ['icon'=>'banknotes', 'title'=>'Competitive Pricing', 'text'=>'Fair, transparent pricing. No hidden charges.'],
                ['icon'=>'shield-check', 'title'=>'Quality Parts', 'text'=>'OEM or quality aftermarket parts only.'],
            ] as $item)
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-brand-red flex items-center justify-center mb-3">
                        <flux:icon name="{{ $item['icon'] }}" class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-1">{{ $item['title'] }}</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Service enquiry form --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Book a Repair or Service</h2>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submitEnquiry" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Full Name *</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <flux:label>Phone *</flux:label>
                <flux:input wire:model="phone" type="tel" />
                <flux:error name="phone" />
            </flux:field>
        </div>
        <flux:field>
            <flux:label>Email *</flux:label>
            <flux:input wire:model="email" type="email" />
            <flux:error name="email" />
        </flux:field>
        <flux:field>
            <flux:label>Service Type</flux:label>
            <flux:select wire:model="serviceType" variant="listbox" placeholder="Select service…">
                @foreach(['basic_service'=>'Basic Service','major_service'=>'Major Service','repair'=>'Repair/Diagnostic','mot'=>'MOT Test','other'=>'Other'] as $val => $label)
                    <flux:select.option value="{{ $val }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:label>Motorcycle Make & Model</flux:label>
            <flux:input wire:model="bikeDetails" placeholder="e.g. Honda PCX 125 (2022)" />
        </flux:field>
        <flux:field>
            <flux:label>Describe the Issue or Work Needed</flux:label>
            <flux:textarea wire:model="notes" rows="4" />
        </flux:field>
        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            Submit Enquiry
        </flux:button>
    </form>
</div>
</div>
