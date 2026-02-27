<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-16 relative overflow-hidden">
    <img src="{{ asset('images/full-service.jpg') }}" alt="Full Service" class="absolute inset-0 w-full h-full object-cover opacity-20">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Full Service</h1>
            <p class="text-xl text-gray-300 mb-4">Comprehensive care package</p>
            <div class="text-5xl font-bold text-brand-red mb-6">From £150</div>
            <flux:button href="/contact/service-booking" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Now</flux:button>
        </div>
    </div>
</div>

{{-- What's Included --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">What's Included</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach([
            ['title' => 'Full Engine Service',       'items' => ['Engine oil change (premium grade)', 'Oil filter replacement', 'Air filter replacement', 'Spark plug inspection/replacement', 'Fuel system cleaning']],
            ['title' => 'Transmission & Drive',      'items' => ['Chain clean, adjust & lubricate', 'Sprocket inspection', 'Clutch adjustment', 'Gearbox oil check', 'Drive system inspection']],
            ['title' => 'Braking System',            'items' => ['Brake fluid check/top-up', 'Brake pad inspection', 'Disc condition check', 'Calliper operation test', 'Brake line inspection']],
            ['title' => 'Suspension & Steering',     'items' => ['Fork seal inspection', 'Shock absorber check', 'Steering head bearings', 'Wheel bearings check', 'Suspension settings']],
            ['title' => 'Electrical System',         'items' => ['Battery test & charge check', 'All lights & indicators', 'Horn operation', 'Wiring inspection', 'Charging system test']],
            ['title' => 'Safety & Extras',           'items' => ['Full safety inspection', 'Tyre pressure & condition', 'Coolant level check', 'Throttle cable adjustment', 'Complete road test']],
        ] as $section)
            <flux:card class="p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <flux:icon name="check-badge" class="h-5 w-5 text-brand-red" />
                    {{ $section['title'] }}
                </h3>
                <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
                    @foreach($section['items'] as $item)
                        <li class="flex items-center gap-2">
                            <flux:icon name="check" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Compare Services CTA --}}
<div class="bg-gray-50 dark:bg-gray-800 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Not sure which service you need?</h2>
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">Compare our service packages to find the right one for your bike</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <flux:button href="/motorbike-service-comparison" variant="outline">Compare Services</flux:button>
            <flux:button href="/contact/service-booking" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Full Service</flux:button>
        </div>
    </div>
</div>
</div>
