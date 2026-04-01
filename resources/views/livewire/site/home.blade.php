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
            ['url'=>'/motorcycle-delivery', 'img'=>'recovery.jpg',   'title'=>'FREE RECOVERY', 'sub'=>'24/7 breakdown assistance'],
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

{{-- Motorcycle rentals: scroll-snap slider (touch + Flux controls), 3 cards per slide --}}
@php $rentalSlides = collect($homeRentalModels)->chunk(3); @endphp
<section
    id="motorcycle-rentals"
    class="relative py-16 md:py-20 border-t border-gray-200 dark:border-gray-800 bg-gradient-to-b from-gray-100 via-gray-50 to-white dark:from-gray-950 dark:via-gray-900 dark:to-gray-950"
    aria-roledescription="carousel"
    aria-label="Rental motorcycle models"
>
    <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10 md:mb-12">
            <flux:badge color="red" class="mb-3 uppercase tracking-widest text-[10px]">Hire fleet</flux:badge>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Motorcycle rentals</h2>
            <p class="mt-3 text-sm md:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                Short-term and long-term hire across our London branches. Swipe on mobile or use the arrows.
                <a href="{{ route('site.rentals') }}" class="text-brand-red font-semibold hover:underline underline-offset-2">Browse the full rental range</a>
            </p>
        </div>

        <div
            x-data="homeRentalCarousel({{ $rentalSlides->count() }})"
            class="relative"
        >
            <div
                x-ref="viewport"
                class="flex w-full overflow-x-auto scroll-smooth snap-x snap-mandatory [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                role="region"
                aria-live="polite"
                @scroll.passive="onScroll()"
                @keydown.left.prevent="step(-1)"
                @keydown.right.prevent="step(1)"
                tabindex="0"
            >
                @foreach($rentalSlides as $si => $chunk)
                    <div
                        class="box-border w-full max-w-full shrink-0 grow-0 snap-start snap-always flex-[0_0_100%] min-w-0 px-0 ml-4"
                        id="rental-slide-{{ $si }}"
                        role="group"
                        aria-roledescription="slide"
                        aria-label="Slide {{ $si + 1 }} of {{ $rentalSlides->count() }}"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 w-full max-w-full min-w-0">
                            @foreach($chunk as $item)
                                <flux:card class="group flex h-full w-full max-w-full min-w-0 flex-col overflow-hidden p-0 border-0 ring-1 ring-gray-200/80 dark:ring-gray-700 shadow-md shadow-gray-900/5 dark:shadow-none hover:shadow-xl hover:ring-brand-red/35 transition-all duration-300 bg-white dark:bg-gray-900">
                                    <a href="{{ $item['href'] }}" class="relative block w-full max-w-full h-56 sm:h-36 md:h-32 lg:h-36 overflow-hidden bg-gray-100 dark:bg-gray-800 outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-inset [max-height:9.5rem] md:[max-height:8.5rem] lg:[max-height:9.5rem]">
                                        <img
                                            loading="lazy"
                                            src="{{ asset($item['img']) }}"
                                            alt="{{ $item['alt'] }}"
                                            width="400"
                                            height="250"
                                            decoding="async"
                                            class="absolute inset-0 h-full w-full max-w-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                        >
                                        <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/75 to-transparent pt-12 pb-3 px-4">
                                            <span class="text-white text-xs font-semibold uppercase tracking-wide opacity-95">From £{{ $item['weekly'] }} / week</span>
                                        </span>
                                    </a>
                                    <div class="flex flex-1 flex-col p-4 md:p-5">
                                        <h3 class="text-center font-bold text-gray-900 dark:text-white text-sm sm:text-base leading-snug mb-1">{{ $item['title'] }}</h3>
                                        <p class="text-center text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">Typical weekly rate for this or a similar model.</p>
                                        <div class="mt-auto flex flex-col gap-2">
                                            <flux:button href="{{ $item['href'] }}" variant="outline" size="sm" class="w-full justify-center ring-1 ring-gray-300 dark:ring-gray-600">More information</flux:button>
                                            <flux:button href="tel:02083141498" variant="filled" size="sm" class="w-full justify-center bg-brand-red text-white hover:bg-brand-red-dark">Call now</flux:button>
                                        </div>
                                    </div>
                                </flux:card>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Side arrows (desktop) --}}
            <div class="pointer-events-none absolute inset-y-0 inset-x-0 z-[5] hidden md:block">
                <div class="relative h-full w-full">
                    <flux:button
                        type="button"
                        variant="filled"
                        class="pointer-events-auto absolute left-0 top-1/2 -translate-y-1/2 -translate-x-2 z-10 h-11 w-11 min-w-11 p-0 bg-white/95 text-gray-900 ring-1 ring-gray-200 shadow-lg hover:bg-white dark:bg-gray-800 dark:text-white dark:ring-gray-600 opacity-90 hover:opacity-100 disabled:opacity-40 disabled:pointer-events-none"
                        x-bind:disabled="atStart"
                        @click="step(-1)"
                        aria-label="Previous rental models"
                    >
                        <flux:icon name="chevron-left" class="size-5 mx-auto" />
                    </flux:button>
                    <flux:button
                        type="button"
                        variant="filled"
                        class="pointer-events-auto absolute right-0 top-1/2 -translate-y-1/2 translate-x-2 z-10 h-11 w-11 min-w-11 p-0 bg-white/95 text-gray-900 ring-1 ring-gray-200 shadow-lg hover:bg-white dark:bg-gray-800 dark:text-white dark:ring-gray-600 opacity-90 hover:opacity-100 disabled:opacity-40 disabled:pointer-events-none"
                        x-bind:disabled="atEnd"
                        @click="step(1)"
                        aria-label="Next rental models"
                    >
                        <flux:icon name="chevron-right" class="size-5 mx-auto" />
                    </flux:button>
                </div>
            </div>

            <div class="mt-8 flex flex-col items-center gap-4">
                <div class="flex items-center justify-center gap-2" role="tablist" aria-label="Slide">
                    @foreach($rentalSlides as $si => $_)
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="index === {{ $si }}"
                            :class="index === {{ $si }} ? 'bg-brand-red w-8' : 'bg-gray-300 dark:bg-gray-600 w-6 hover:bg-gray-400 dark:hover:bg-gray-500'"
                            class="h-1 max-w-8 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                            aria-label="Go to slide {{ $si + 1 }}"
                            @click="goTo({{ $si }})"
                        ></button>
                    @endforeach
                </div>
                <div class="flex md:hidden items-center justify-center gap-3">
                    <flux:button type="button" variant="outline" size="sm" class="min-w-[7rem] disabled:opacity-40" x-bind:disabled="atStart" @click="step(-1)">Previous</flux:button>
                    <flux:button type="button" variant="outline" size="sm" class="min-w-[7rem] disabled:opacity-40" x-bind:disabled="atEnd" @click="step(1)">Next</flux:button>
                </div>
            </div>
        </div>
    </div>
</section>

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

{{-- Legacy parity block: keep current home and add old-style sales/news/contact --}}
<div class="py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Used Bikes For Sale</h2>
            <a href="{{ route('motorcycles.used') }}" class="text-brand-red hover:text-red-700 text-sm font-medium">See all used bikes →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($usedBikesForSale as $bike)
                <article class="border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="block">
                        @php $img = $bike->image_one ? 'https://neguinhomotors.co.uk/storage/motorbikes/'.$bike->image_one : 'https://neguinhomotors.co.uk/assets/img/no-image.png'; @endphp
                        <img src="{{ $img }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $bike->make }} {{ $bike->model }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">****{{ substr((string) $bike->reg_no, -3) }}</p>
                        <p class="text-brand-red font-bold mt-1">GBP {{ number_format((float) $bike->price, 2) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-gray-50 dark:bg-gray-900 py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Latest News</h2>
                <div class="space-y-3">
                    @foreach($blogPosts as $post)
                        <a href="/shop/blog/{{ $post->slug }}" class="block border border-gray-200 dark:border-gray-700 hover:border-brand-red transition">
                            <div class="flex">
                                @php $blogImage = $post->images->isNotEmpty() ? 'https://neguinhomotors.co.uk/storage/'.$post->images->first()->path : 'https://neguinhomotors.co.uk/assets/img/no-image.png'; @endphp
                                <img src="{{ $blogImage }}" alt="{{ $post->title }}" class="w-24 h-24 object-cover">
                                <div class="p-3">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $post->title }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 95) }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Contact Us</h2>
                <div class="border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">For sales, rentals, repairs, MOT, e-bikes and finance enquiries.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <flux:button href="{{ route('site.contact') }}" variant="outline" class="w-full">General contact</flux:button>
                        <flux:button href="{{ route('site.service.booking') }}" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">Order / enquiry form</flux:button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

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
