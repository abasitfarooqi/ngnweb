<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Motorcycle Repair Services</h1>
        <p class="text-xl text-red-100 mb-8">Expert diagnostics & repairs by qualified mechanics</p>
        <flux:button href="/contact/service-booking" variant="filled" class="bg-white text-brand-red hover:bg-gray-100">Book a Repair</flux:button>
    </div>
</div>

{{-- Repair Services Grid --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">Repair Services We Offer</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach([
            ['title' => 'Diagnostic Services',   'price' => 'From £40',              'items' => ['Fault code reading & clearing', 'Engine diagnostic testing', 'Electrical fault finding', 'Performance issues', 'Strange noises & vibrations']],
            ['title' => 'Chain & Sprockets',      'price' => 'From £120 (inc parts)', 'items' => ['Chain replacement', 'Sprocket replacement (front & rear)', 'Chain & sprocket kit fitting', 'Chain adjustment & lubrication', 'Tension inspection']],
            ['title' => 'Brake Services',         'price' => 'From £60 (exc parts)',  'items' => ['Brake pad replacement', 'Brake disc replacement', 'Brake fluid flush & replacement', 'Calliper servicing & rebuild', 'Brake line replacement']],
            ['title' => 'Tyre Services',          'price' => 'From £50 (exc tyre)',   'items' => ['Tyre replacement (all brands)', 'Wheel balancing', 'Puncture repair', 'Valve replacement', 'Tyre disposal']],
            ['title' => 'Electrical Work',        'price' => 'From £50',              'items' => ['Battery replacement & testing', 'Alternator repairs', 'Wiring repairs & upgrades', 'Lighting upgrades', 'Ignition system repairs']],
            ['title' => 'Clutch & Gearbox',       'price' => 'From £80',              'items' => ['Clutch cable replacement', 'Clutch plate replacement', 'Gearbox oil change', 'Gear linkage adjustment', 'Clutch lever replacement']],
            ['title' => 'Exhaust Systems',        'price' => 'From £60',              'items' => ['Exhaust repair & welding', 'Complete exhaust replacement', 'Silencer replacement', 'Lambda sensor replacement', 'Heat shield repairs']],
            ['title' => 'Suspension Work',        'price' => 'From £100',             'items' => ['Fork seal replacement', 'Fork oil change', 'Shock absorber servicing', 'Suspension setup & tuning', 'Linkage bearing replacement']],
        ] as $service)
            <flux:card class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $service['title'] }}</h3>
                <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300 mb-4">
                    @foreach($service['items'] as $item)
                        <li class="flex items-center gap-2">
                            <flux:icon name="check" class="h-4 w-4 text-green-500 flex-shrink-0" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <p class="text-brand-red font-bold">{{ $service['price'] }}</p>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Why Choose Us --}}
<div class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-12 text-center">Why Choose NGN for Repairs?</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['icon' => 'wrench-screwdriver', 'title' => 'Expert Technicians', 'desc' => 'Factory-trained mechanics'],
                ['icon' => 'check-badge',         'title' => 'Quality Parts',       'desc' => 'OEM & premium brands'],
                ['icon' => 'bolt',                'title' => 'Fast Service',         'desc' => 'Most repairs same day'],
                ['icon' => 'banknotes',            'title' => 'Competitive Prices',  'desc' => 'No hidden charges'],
            ] as $why)
                <div>
                    <flux:icon name="{{ $why['icon'] }}" class="h-10 w-10 text-brand-red mx-auto mb-3" />
                    <h3 class="text-xl font-bold mb-2">{{ $why['title'] }}</h3>
                    <p class="text-gray-300 text-sm">{{ $why['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-brand-red text-white py-10 text-center">
    <h2 class="text-2xl font-bold mb-4">Book a Repair Today</h2>
    <p class="text-red-100 mb-6">Bring your bike to any of our three London branches</p>
    <div class="flex flex-wrap gap-3 justify-center">
        <flux:button href="/contact/service-booking" variant="filled" class="bg-white text-brand-red hover:bg-gray-100">Book Online</flux:button>
        <flux:button href="tel:02083141498" variant="outline" class="border-white text-white hover:bg-white hover:text-brand-red">Call 0208 314 1498</flux:button>
    </div>
</div>
</div>
