<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycle Rentals in London</h1>
        <p class="text-gray-300 text-lg mb-6">From £80/week · Honda & Yamaha 125cc · CBT friendly</p>
        <flux:button href="/contact" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Get a Quote
        </flux:button>
    </div>
</div>

{{-- Legacy static rental highlights (kept with current dynamic listing) --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Prices Start From GBP 70/Week</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach([
            ['slug' => 'honda-forza-125', 'name' => 'Honda Forza 125cc', 'price' => 100, 'img' => '/img/rentals/honda-forza-125.jpg'],
            ['slug' => 'honda-pcx-125', 'name' => 'Honda PCX 125cc', 'price' => 75, 'img' => '/img/rentals/honda-pcx-125.jpg'],
            ['slug' => 'honda-sh-125', 'name' => 'Honda SH 125cc', 'price' => 75, 'img' => '/img/rentals/honda-sh-125.jpg'],
            ['slug' => 'honda-vision-125', 'name' => 'Honda Vision 125cc', 'price' => 70, 'img' => '/img/rentals/honda-vision-125.jpg'],
            ['slug' => 'yamaha-nmax-125', 'name' => 'Yamaha NMAX 125cc', 'price' => 75, 'img' => '/img/rentals/yamaha-nmax-125.jpg'],
            ['slug' => 'yamaha-xmax-125', 'name' => 'Yamaha XMAX 125cc', 'price' => 100, 'img' => '/img/rentals/yamaha-xmax-125.jpg'],
        ] as $item)
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden">
                <a href="/{{ $item['slug'] }}" class="block">
                    <img src="{{ url($item['img']) }}" alt="{{ $item['name'] }}" class="w-full h-52 object-cover">
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                    <p class="text-brand-red font-bold mt-1">From GBP {{ number_format((float) $item['price'], 2) }} per week</p>
                    <flux:button href="/{{ $item['slug'] }}" variant="outline" size="sm" class="w-full mt-3">More information</flux:button>
                </div>
            </article>
        @endforeach
    </div>
</div>
{{-- 
    Rental Grid 

    This section is intentionally commented out to safely hide it and avoid the "Multiple root elements detected for component" Livewire error.
    To restore, uncomment it and ensure this file has only one root element (a single parent <div> wrapping the entire template).
--}}

{{--
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Available Motorcycles</h2>

    @if(count($rentals) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($rentals as $rental)
                <flux:card class="overflow-hidden p-0">
                    <div class="bg-gray-100 dark:bg-gray-800 h-48"></div>
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $rental->make }} {{ $rental->model }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $rental->engine ?? 'N/A' }} · {{ $rental->year ?? 'Current Year' }}</p>
                        <p class="text-brand-red text-2xl font-bold mb-4">£{{ number_format($rental->currentRentingPricing->weekly_price ?? 80, 0) }}/week</p>
                        <flux:button href="/rentals/{{ $rental->id }}" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                            View Details
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-gray-500 dark:text-gray-400 mb-4">No rentals available at the moment. Please check back soon or contact us.</p>
            <flux:button href="/contact" variant="filled" class="bg-brand-red text-white">Contact Us</flux:button>
        </div>
    @endif
</div>
--}}

{{-- Requirements --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Rental Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <flux:card class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">What You Need</h3>
                <ul class="space-y-3">
                    @foreach([
                        'Valid motorcycle licence or CBT certificate',
                        'Proof of address (utility bill or bank statement)',
                        'Valid photo ID (passport or driving licence)',
                        'Refundable security deposit',
                    ] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </flux:card>

            <flux:card class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">What's Included</h3>
                <ul class="space-y-3">
                    @foreach([
                        'Fully serviced & MOT\'d motorcycle',
                        'Insurance (third party, fire & theft)',
                        'Road tax',
                        '24/7 breakdown assistance',
                    ] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <flux:callout variant="warning" icon="information-circle" class="mt-4 text-xs">
                    Lock & chain must be purchased from our shop.
                </flux:callout>
            </flux:card>

        </div>
    </div>
</div>

{{-- CTA --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Ready to rent? We have flexible plans for commuters & weekend riders.</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">
            Get a Quote
        </flux:button>
    </div>
</div>
</div>
