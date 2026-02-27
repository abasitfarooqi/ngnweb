<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <img src="{{ asset('images/gps-tracker.jpg') }}" alt="GPS Tracker" class="w-full h-64 object-cover mb-8 opacity-20 absolute inset-0" style="display:none">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">GPS Tracker Installation</h1>
        <p class="text-xl text-red-100 mb-8">Protect your motorcycle from theft</p>
        <flux:button href="/contact" variant="filled" class="bg-white text-brand-red hover:bg-gray-100">Get a Quote</flux:button>
    </div>
</div>

{{-- Benefits --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">Why Fit a GPS Tracker?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach([
            ['icon' => 'shield-check',   'title' => 'Theft Protection',    'desc' => "Track your bike's location 24/7. Get instant alerts if moved without authorisation."],
            ['icon' => 'banknotes',       'title' => 'Insurance Discount',  'desc' => 'Save up to 25% on your insurance premium with an approved tracker fitted.'],
            ['icon' => 'magnifying-glass','title' => 'Recovery Support',    'desc' => 'Police support and dedicated recovery team if your bike is stolen.'],
        ] as $benefit)
            <flux:card class="p-8 text-center">
                <flux:icon name="{{ $benefit['icon'] }}" class="h-12 w-12 text-brand-red mx-auto mb-4" />
                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-3">{{ $benefit['title'] }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $benefit['desc'] }}</p>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Tracker Options --}}
<div class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Available GPS Trackers</h2>
        <div class="space-y-6">
            @foreach([
                ['name' => 'Monimoto 7 GPS Tracker', 'desc' => "Premium tracker with 2-year battery & SMS alerts", 'badge' => 'Thatcham Approved', 'price' => '£249', 'install' => '+ Installation £50'],
                ['name' => 'Datatool Trak-King',      'desc' => "Professional tracker with 24/7 monitoring",       'badge' => 'Category S5',       'price' => '£349', 'install' => '+ Installation £75 + £99/year'],
            ] as $tracker)
                <flux:card class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $tracker['name'] }}</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $tracker['desc'] }}</p>
                            <flux:badge color="green">{{ $tracker['badge'] }}</flux:badge>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-3xl font-bold text-brand-red mb-1">{{ $tracker['price'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tracker['install'] }}</p>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </div>
</div>

{{-- Features --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Tracker Features</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach([
            ['title' => 'Real-time Tracking',       'desc' => 'Track location via app'],
            ['title' => 'Movement Alerts',           'desc' => 'Instant SMS/App notification'],
            ['title' => 'Geofence Zones',            'desc' => 'Alert if bike leaves area'],
            ['title' => 'Professional Installation', 'desc' => 'Expert fitting by technicians'],
        ] as $feature)
            <div class="flex items-start gap-3">
                <flux:icon name="check-circle" class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $feature['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-gray-900 text-white py-10 text-center">
    <h2 class="text-2xl font-bold mb-4">Book GPS Tracker Installation</h2>
    <p class="text-gray-300 mb-6">We install at all three NGN branches in London</p>
    <flux:button href="/contact" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Now</flux:button>
</div>
</div>
