<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Motorcycle Accessories</h1>
        <p class="text-xl text-red-100">Everything you need for your ride</p>
    </div>
</div>

{{-- Categories --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['icon' => 'shield-check', 'title' => 'Helmets',     'desc' => 'Full face, open face & modular'],
            ['icon' => 'user',         'title' => 'Riding Gear', 'desc' => 'Jackets, pants & gloves'],
            ['icon' => 'archive-box',  'title' => 'Luggage',     'desc' => 'Top boxes, panniers & bags'],
            ['icon' => 'lock-closed',  'title' => 'Security',    'desc' => 'Locks, chains & alarms'],
            ['icon' => 'wrench-screwdriver', 'title' => 'Maintenance', 'desc' => 'Tools, oils & cleaners'],
            ['icon' => 'device-phone-mobile', 'title' => 'Electronics', 'desc' => 'GPS, intercoms & cameras'],
            ['icon' => 'light-bulb',   'title' => 'Lighting',    'desc' => 'LED upgrades & auxiliary'],
            ['icon' => 'paint-brush',  'title' => 'Styling',     'desc' => 'Graphics, mirrors & more'],
        ] as $cat)
            <flux:card class="p-6 text-center hover:border-brand-red transition">
                <flux:icon name="{{ $cat['icon'] }}" class="h-10 w-10 text-brand-red mx-auto mb-3" />
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $cat['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $cat['desc'] }}</p>
                <flux:button variant="outline" size="sm" class="w-full" href="/contact">Browse</flux:button>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="bg-gray-50 dark:bg-gray-800 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Can't Find What You Need?</h2>
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">Contact us and we'll source it for you</p>
        <flux:button href="/contact" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Get in Touch</flux:button>
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-gray-900 text-white py-10 text-center">
    <h2 class="text-2xl font-bold mb-4">Need Help Choosing?</h2>
    <p class="text-gray-300 mb-6">Our team is on hand to advise – call us or drop in at any branch</p>
    <div class="flex flex-wrap gap-3 justify-center">
        <flux:button href="tel:02083141498" variant="filled" class="bg-brand-red text-white">Call Catford: 0208 314 1498</flux:button>
        <flux:button href="/contact" variant="outline" class="border-white text-white hover:bg-white hover:text-gray-900">Send a Message</flux:button>
    </div>
</div>
</div>
