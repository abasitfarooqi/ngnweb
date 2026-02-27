<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-16 relative overflow-hidden">
    <img src="{{ asset('images/basic-service.jpg') }}" alt="Basic Service" class="absolute inset-0 w-full h-full object-cover opacity-20">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg">
            <flux:badge color="red" class="mb-4">Most Popular</flux:badge>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Basic Service</h1>
            <p class="text-xl text-gray-300 mb-4">Perfect for regular maintenance</p>
            <div class="text-5xl font-bold text-brand-red mb-6">From £80</div>
            <flux:button href="/contact/service-booking" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Now</flux:button>
        </div>
    </div>
</div>

{{-- What's Included --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">What's Included</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach([
            ['icon' => 'beaker',            'title' => 'Engine Oil Change',   'desc' => 'Fresh engine oil & new oil filter fitted'],
            ['icon' => 'stop-circle',       'title' => 'Brake Inspection',    'desc' => 'Full brake system check & adjustment'],
            ['icon' => 'arrows-pointing-in','title' => 'Tyre Check',          'desc' => 'Pressure check & tread depth inspection'],
            ['icon' => 'link',              'title' => 'Chain/Belt Service',  'desc' => 'Clean, adjust & lubricate drive chain'],
            ['icon' => 'bolt',              'title' => 'Electrical Check',    'desc' => 'Lights, indicators & battery test'],
            ['icon' => 'shield-check',      'title' => 'Safety Inspection',   'desc' => 'Comprehensive safety check'],
        ] as $item)
            <flux:card class="p-6">
                <div class="flex items-start gap-4">
                    <flux:icon name="{{ $item['icon'] }}" class="h-7 w-7 text-brand-red flex-shrink-0 mt-0.5" />
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $item['desc'] }}</p>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Service Interval --}}
<div class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">When Do I Need a Basic Service?</h2>
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">We recommend a basic service every 6 months or 3,000 miles, whichever comes first.</p>
        <div class="bg-white dark:bg-gray-900 p-8 shadow-sm max-w-sm mx-auto border border-gray-200 dark:border-gray-700">
            <flux:icon name="clock" class="h-10 w-10 text-brand-red mx-auto mb-3" />
            <div class="text-4xl font-bold text-brand-red mb-2">1–2 Hours</div>
            <p class="text-gray-600 dark:text-gray-400">Most basic services completed while you wait</p>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Ready to Book?</h2>
    <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">Book your basic service online or call us today</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <flux:button href="/contact/service-booking" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Online</flux:button>
        <flux:button href="/repairs" variant="outline">View All Services</flux:button>
    </div>
</div>
</div>
