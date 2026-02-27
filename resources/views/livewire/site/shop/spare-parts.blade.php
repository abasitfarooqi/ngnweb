<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Spare Parts</h1>
        <p class="text-xl text-red-100 mb-8">Genuine & pattern parts for all makes & models</p>
        <flux:button href="/contact" variant="filled" class="bg-white text-brand-red hover:bg-gray-100">Request a Quote</flux:button>
    </div>
</div>

{{-- Parts Categories --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">Browse by Category</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['title' => 'Engine Parts',    'items' => ['Pistons & rings', 'Gaskets & seals', 'Oil filters', 'Air filters', 'Spark plugs', 'Clutch plates']],
            ['title' => 'Braking System',  'items' => ['Brake pads', 'Brake discs', 'Brake fluid', 'Callipers', 'Brake lines', 'Master cylinders']],
            ['title' => 'Transmission',    'items' => ['Drive chains', 'Sprockets', 'Chain kits', 'Belt drives', 'Clutch cables', 'Gearbox oil']],
            ['title' => 'Suspension',      'items' => ['Fork seals', 'Fork oil', 'Shock absorbers', 'Bearings', 'Springs', 'Bushings']],
            ['title' => 'Electrical',      'items' => ['Batteries', 'Bulbs & LEDs', 'Stators & regulators', 'Switches', 'Fuses', 'Wiring harnesses']],
            ['title' => 'Body & Fairings', 'items' => ['Fairings & panels', 'Screens & windshields', 'Mirrors', 'Seats', 'Indicators', 'Number plate holders']],
        ] as $cat)
            <flux:card class="p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon name="wrench-screwdriver" class="h-5 w-5 text-brand-red" />
                    {{ $cat['title'] }}
                </h3>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    @foreach($cat['items'] as $item)
                        <li class="flex items-center gap-2">
                            <flux:icon name="check" class="h-4 w-4 text-green-500 flex-shrink-0" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Why Buy From Us --}}
<div class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-12 text-center">Why Buy Parts from NGN?</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['icon' => 'check-badge', 'title' => 'Genuine Parts',    'desc' => 'OEM quality guaranteed'],
                ['icon' => 'banknotes',   'title' => 'Best Prices',       'desc' => 'Competitive pricing'],
                ['icon' => 'cube',        'title' => 'Fast Delivery',     'desc' => 'Next day available'],
                ['icon' => 'wrench-screwdriver', 'title' => 'Fitting Service', 'desc' => 'We can fit for you'],
            ] as $benefit)
                <div>
                    <flux:icon name="{{ $benefit['icon'] }}" class="h-10 w-10 text-brand-red mx-auto mb-3" />
                    <h3 class="text-xl font-bold mb-2">{{ $benefit['title'] }}</h3>
                    <p class="text-gray-300 text-sm">{{ $benefit['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-gray-50 dark:bg-gray-800 py-10 text-center">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Can't Find Your Part?</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Give us the part number or description — we'll source it</p>
    <flux:button href="/contact" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Request a Part</flux:button>
</div>
</div>
