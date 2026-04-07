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
                <flux:button href="#locations" variant="outline" size="base" class="border-white text-white hover:bg-white hover:text-gray-900">
                    Find your branch
                </flux:button>
            </div>
        </div>
    </div>
</div>

@include('livewire.site.home.partials.mot-recovery-club')
@include('livewire.site.home.partials.ebikes-home')

{{-- Quick links: avoids duplicating recovery / club / rentals / MOT tiles above --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('site.bikes') }}" class="relative group block overflow-hidden h-64 md:h-72 mb-4">
        <img src="{{ asset('images/for-sale.jpg') }}" alt="Motorcycles for sale" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition-all"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-4">
            <h2 class="text-3xl md:text-4xl font-bold mb-2">MOTORCYCLES FOR SALE</h2>
            <p class="text-gray-200 text-sm">New & used · Finance available</p>
        </div>
    </a>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['url' => route('shop.home'), 'img' => 'spare-parts.jpg', 'title' => 'SPARE PARTS', 'sub' => 'Honda & Yamaha parts'],
            ['url' => route('site.repairs'), 'img' => 'services.jpg', 'title' => 'ALL SERVICES', 'sub' => 'Repairs, servicing & more'],
            ['url' => route('site.finance'), 'img' => 'finance.jpg', 'title' => 'FINANCE', 'sub' => 'Flexible payment plans'],
        ] as $box)
            <a href="{{ $box['url'] }}" class="relative group overflow-hidden h-52 block">
                <img src="{{ asset('images/' . $box['img']) }}" alt="{{ $box['title'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition-all"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center p-4">
                    <h2 class="text-lg md:text-xl font-bold mb-1">{{ $box['title'] }}</h2>
                    <p class="text-gray-300 text-xs">{{ $box['sub'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>

@include('livewire.site.home.partials.repairs-services-home-grid')

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

{{-- Used motorcycles (same stock as /used-motorcycles; five on home) --}}
@if($usedBikesForSale->isNotEmpty())
<section class="relative py-16 md:py-20 border-t border-gray-200 dark:border-gray-800 bg-gradient-to-b from-white via-gray-50 to-gray-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950" aria-label="Used motorcycles for sale">
    <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <flux:badge color="red" class="mb-3 uppercase tracking-widest text-[10px]">Used stock</flux:badge>
                <h2 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Used motorcycles</h2>
                <p class="mt-2 text-sm md:text-base text-gray-600 dark:text-gray-400 max-w-2xl leading-relaxed">
                    A sample of our current used bikes. Same listings as our full used showroom.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <flux:button href="{{ route('used-motorcycles.page') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">
                    See more
                </flux:button>
                <flux:button href="{{ route('site.bikes', ['filter' => 'used']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200">
                    All used on /bikes
                </flux:button>
            </div>
        </div>
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-5 list-none p-0 m-0" role="list">
            @foreach($usedBikesForSale as $bike)
                @php
                    $img = \App\Support\NgnMotorcycleImage::urlForUsedSale($bike->image_one ?? null);
                    $mileage = $bike->sale_mileage ?? $bike->mileage ?? null;
                @endphp
                <li class="min-w-0">
                    <flux:card class="group flex h-full flex-col overflow-hidden p-0 border-0 ring-1 ring-gray-200/90 dark:ring-gray-700 bg-white dark:bg-gray-900 shadow-md shadow-gray-900/5 dark:shadow-none hover:shadow-xl hover:ring-brand-red/40 transition-all duration-300">
                        <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="relative block w-full aspect-[5/3] min-h-[10rem] bg-gray-100 dark:bg-gray-800 overflow-hidden outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-inset">
                            <img
                                src="{{ $img }}"
                                alt="{{ $bike->make }} {{ $bike->model }}"
                                width="400"
                                height="240"
                                loading="lazy"
                                decoding="async"
                                class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                            >
                            <span class="absolute top-2 left-2">
                                <flux:badge color="zinc" class="text-[10px] uppercase tracking-wide bg-black/70 text-white border-0">Used</flux:badge>
                            </span>
                        </a>
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm leading-snug line-clamp-2 min-h-[2.5rem]">{{ $bike->make }} {{ $bike->model }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if($mileage !== null && $mileage !== ''){{ number_format((int) $mileage) }} miles · @endif{{ $bike->year ?? '—' }}
                            </p>
                            <p class="text-brand-red font-black text-lg mt-2">£{{ number_format((float) ($bike->price ?? 0), 0) }}</p>
                            <div class="mt-auto pt-3">
                                <flux:button href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" variant="outline" size="sm" class="w-full justify-center ring-1 ring-gray-300 dark:ring-gray-600">
                                    View details
                                </flux:button>
                            </div>
                        </div>
                    </flux:card>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

@include('livewire.site.home.partials.finance-installment-strip')

<div class="bg-gray-50 dark:bg-gray-900 py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Latest News</h2>
                <div class="space-y-3 mb-4">
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
                <div class="text-right">
                    <a href="/shop/blog" class="text-sm font-semibold text-brand-red hover:underline">See all our latest updates</a>
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Contact Us</h2>
                @if(session('success') && ! session('newsletter_ok'))
                    <flux:callout variant="success" icon="check-circle" class="mb-4">
                        <flux:callout.text>{{ session('success') }}</flux:callout.text>
                    </flux:callout>
                @endif
                <flux:card class="p-5 md:p-6 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 ring-1 ring-gray-200/80 dark:ring-gray-700/80 shadow-sm">
                    <form wire:submit="submitContact" class="space-y-4">
                        <flux:field>
                            <flux:label>Name *</flux:label>
                            <flux:input wire:model="contactName" autocomplete="name" />
                            @error('contactName')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>Email</flux:label>
                            <flux:input wire:model="contactEmail" type="email" autocomplete="email" />
                            @error('contactEmail')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>Phone *</flux:label>
                            <flux:input wire:model="contactPhone" type="tel" autocomplete="tel" />
                            @error('contactPhone')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>Subject *</flux:label>
                            <flux:input wire:model="contactSubject" placeholder="What do you want to know?" />
                            @error('contactSubject')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>Message *</flux:label>
                            <flux:textarea wire:model="contactMessage" rows="5" />
                            @error('contactMessage')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </flux:field>
                        <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitContact">Send</span>
                            <span wire:loading wire:target="submitContact">Sending…</span>
                        </flux:button>
                    </form>
                </flux:card>
            </section>
        </div>
    </div>
</div>

{{-- About (legacy: aboutHomeSection — full copy) --}}
<div class="bg-[#1f1f1f] text-white py-14 md:py-16 border-t border-gray-800" id="about-us">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold mb-6 tracking-tight">About NGN</h2>
        <div class="text-sm md:text-base text-gray-300 leading-relaxed space-y-4 text-left md:text-center">
            <p>
                Incorporated in October 2018, NGN is specialise in <strong class="text-white">motorcycle rentals</strong>, <strong class="text-white">MOT services</strong>, sales, <strong class="text-white">spare parts</strong>, <strong class="text-white">maintenance</strong>, and <strong class="text-white">repair services</strong>, offering a wide range of motorcycle accessories in <strong class="text-white">London</strong>, <strong class="text-white">Catford</strong>, <strong class="text-white">Tooting</strong>, and <strong class="text-white">Sutton</strong>. Our mission is to keep your motorcycle roadworthy and performing at its best.
            </p>
            <p>
                Whether you need quality motorcycle rentals or expert repairs, we are here to provide top-notch motorcycling solutions and exceptional customer support. Our commitment to excellence ensures that every rider receives the best possible service, making NGN the go-to destination for all your motorcycle needs.
            </p>
        </div>
        <flux:button href="{{ route('site.about') }}" variant="outline" class="mt-8 border-white text-white hover:bg-white hover:text-gray-900">
            Learn more about us
        </flux:button>
    </div>
</div>

@include('livewire.site.home.partials.locations-stores-gallery')
@include('livewire.site.home.partials.newsletter-social')
</div>
