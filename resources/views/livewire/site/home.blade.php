<div>
{{-- Hero --}}
<div class="relative bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-black via-gray-900 to-brand-red opacity-90"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="max-w-2xl">
            <flux:badge color="red" class="mb-4 uppercase tracking-wide text-xs">London's Motorcycle Specialists Since 2018</flux:badge>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                Motorcycles.<br>Rentals, MOT,<br>
                <span class="text-red-400">Repairs & Sales.</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-8">
                Serving Catford, Tooting & Sutton. From daily commuters to full-service mechanics – we've got every rider covered.
            </p>
            <div class="flex flex-wrap gap-3">
                <flux:button
                    x-data
                    @click="$flux.modal('quick-book').show()"
                    variant="filled"
                    size="base"
                    class="bg-brand-red text-white hover:bg-brand-red-dark"
                >
                    Book Now
                </flux:button>
                <flux:button href="/locations" variant="outline" size="base" class="border-white text-white hover:bg-white hover:text-gray-900">
                    Find Your Branch
                </flux:button>
            </div>
        </div>
    </div>
</div>

{{-- Main Services Grid --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <a href="/bikes" class="md:col-span-2 lg:col-span-2 relative group overflow-hidden h-80 block">
            <img src="{{ asset('images/for-sale.jpg') }}" alt="Motorcycles For Sale" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition-all"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-2">MOTORCYCLES FOR SALE</h2>
                <p class="text-gray-200 text-sm">New & used | Finance available</p>
            </div>
        </a>

        <a href="/club" class="relative group overflow-hidden h-80 block">
            <img src="{{ asset('images/ngn-club.jpg') }}" alt="NGN Club" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition-all"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-4">
                <span class="text-amber-400 text-2xl mb-2">★</span>
                <h2 class="text-2xl md:text-3xl font-bold">JOIN NGN CLUB</h2>
                <p class="text-gray-200 text-sm mt-1">Exclusive rewards & discounts</p>
            </div>
        </a>

        @foreach([
            ['url'=>'/rentals',  'img'=>'rentals.jpg',    'title'=>'RENTALS',       'sub'=>'From £80/week · 125cc · CBT friendly'],
            ['url'=>'/recovery', 'img'=>'recovery.jpg',   'title'=>'FREE RECOVERY', 'sub'=>'24/7 breakdown assistance'],
            ['url'=>'/shop',     'img'=>'spare-parts.jpg','title'=>'SPARE PARTS',   'sub'=>'Honda & Yamaha parts'],
            ['url'=>'/repairs',  'img'=>'services.jpg',   'title'=>'ALL SERVICES',  'sub'=>'Repairs, servicing & more'],
            ['url'=>'/mot',      'img'=>'mot.jpg',        'title'=>'MOT TESTING',   'sub'=>'From £29.65'],
            ['url'=>'/finance',  'img'=>'finance.jpg',    'title'=>'FINANCE',       'sub'=>'Flexible payment plans'],
        ] as $box)
            <a href="{{ $box['url'] }}" class="relative group overflow-hidden h-56 block">
                <img src="{{ asset('images/' . $box['img']) }}" alt="{{ $box['title'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition-all"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-4">
                    <h2 class="text-xl md:text-2xl font-bold mb-1">{{ $box['title'] }}</h2>
                    <p class="text-gray-300 text-xs">{{ $box['sub'] }}</p>
                </div>
            </a>
        @endforeach

    </div>
</div>

{{-- The NGN Advantage --}}
<div class="bg-gray-50 dark:bg-gray-900 py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">The NGN Advantage</h2>
            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">Why thousands of London riders choose us</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            @foreach([
                ['icon'=>'check-badge', 'title'=>'Unmatched Customer Support', 'text'=>'Exceptional service across all three London branches. Our team goes the extra mile for every rider.'],
                ['icon'=>'wrench-screwdriver', 'title'=>'Quality Assurance', 'text'=>'Only the best parts and latest technology. Every motorcycle leaves in peak condition.'],
                ['icon'=>'clock', 'title'=>'Convenience & Flexibility', 'text'=>'Flexible rental periods, on-site repairs and delivery options to fit your schedule.'],
            ] as $item)
                <div>
                    <div class="w-14 h-14 bg-brand-red flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="{{ $item['icon'] }}" class="h-7 w-7 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Featured Rentals --}}
@if(count($featuredRentals) > 0)
<div class="py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Popular Rentals</h2>
            <a href="/rentals" class="text-brand-red hover:text-red-700 text-sm font-medium">View all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredRentals as $rental)
                <flux:card class="overflow-hidden p-0">
                    <div class="bg-gray-100 dark:bg-gray-800 h-44">
                        @if($rental->images && $rental->images->count() > 0)
                            <img src="{{ $rental->images->first()->url ?? asset('images/placeholder-bike.jpg') }}"
                                 alt="{{ $rental->make }} {{ $rental->model }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">No image</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $rental->make }} {{ $rental->model }}</h3>
                        <p class="text-brand-red font-bold mb-3">From £{{ number_format($rental->currentRentingPricing->weekly_price ?? 80, 0) }}/week</p>
                        <flux:button href="/rentals/{{ $rental->id }}" variant="outline" size="sm" class="w-full">View Details</flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Bikes For Sale --}}
@if(count($newBikesForSale) > 0 || count($usedBikesForSale) > 0)
<div class="bg-gray-50 dark:bg-gray-900 py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Motorcycles For Sale</h2>
            <a href="/bikes" class="text-brand-red hover:text-red-700 text-sm font-medium">View all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($newBikesForSale->take(2) as $bike)
                <flux:card class="overflow-hidden p-0">
                    <div class="relative bg-gray-100 dark:bg-gray-800 h-44">
                        <flux:badge color="green" class="absolute top-2 right-2 text-xs">New</flux:badge>
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">{{ $bike->make }} {{ $bike->model }}</div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->make }} {{ $bike->model }}</h3>
                        <p class="text-xs text-gray-500 mb-2">{{ $bike->engine_capacity ?? 'N/A' }} · {{ $bike->year ?? 'Current' }}</p>
                        <p class="text-brand-red font-bold text-lg mb-3">£{{ number_format($bike->price ?? 0, 0) }}</p>
                        <flux:button href="/bikes/new/{{ $bike->id }}" variant="outline" size="sm" class="w-full">View Details</flux:button>
                    </div>
                </flux:card>
            @endforeach
            @foreach($usedBikesForSale->take(2) as $bike)
                <flux:card class="overflow-hidden p-0">
                    <div class="relative bg-gray-100 dark:bg-gray-800 h-44">
                        <flux:badge color="blue" class="absolute top-2 right-2 text-xs">Used</flux:badge>
                        @if($bike->image_one)
                            <img src="{{ $bike->image_one }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">{{ $bike->make }} {{ $bike->model }}</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->make }} {{ $bike->model }}</h3>
                        <p class="text-xs text-gray-500 mb-2">{{ $bike->engine ?? 'N/A' }} · {{ $bike->year ?? 'N/A' }}</p>
                        <p class="text-brand-red font-bold text-lg mb-3">£{{ number_format($bike->price ?? 0, 0) }}</p>
                        <flux:button href="/bikes/used/{{ $bike->id }}" variant="outline" size="sm" class="w-full">View Details</flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- About strip --}}
<div class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">About NGN Motors</h2>
            <p class="text-gray-300 mb-4">
                Incorporated in October 2018, NGN specialises in <strong>motorcycle rentals</strong>, <strong>MOT services</strong>, sales, spare parts, maintenance and repairs across <strong>London</strong> – Catford, Tooting and Sutton.
            </p>
            <p class="text-gray-400 text-sm mb-8">
                Our mission is to keep your motorcycle roadworthy and performing at its best with top-notch service and exceptional customer support.
            </p>
            <flux:button href="/about" variant="outline" class="border-white text-white hover:bg-white hover:text-gray-900">
                Learn More About Us
            </flux:button>
        </div>
    </div>
</div>

{{-- Branches --}}
<div class="py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Our London Branches</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($branches as $branch)
                @php
                    $phone   = $branch->phone    ?? config('site.branches.' . strtolower($branch->name) . '.phone');
                    $address = $branch->address  ?? config('site.branches.' . strtolower($branch->name) . '.address');
                @endphp
                <flux:card class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $branch->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $address }}</p>
                    <a href="tel:{{ $phone }}" class="text-brand-red font-medium text-sm hover:underline">{{ $phone }}</a>
                </flux:card>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">Ready to ride with NGN?</h2>
            <p class="text-red-100 text-sm">Get in touch or visit one of our branches today.</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <flux:button
                x-data
                @click="$flux.modal('quick-book').show()"
                variant="filled"
                class="bg-white text-brand-red hover:bg-gray-100 font-semibold"
            >
                Book Now
            </flux:button>
            <flux:button href="/contact" variant="outline" class="border-white text-white hover:bg-white hover:text-brand-red">
                Contact Us
            </flux:button>
        </div>
    </div>
</div>
</div>
