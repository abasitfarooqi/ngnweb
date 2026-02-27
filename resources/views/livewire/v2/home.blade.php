<div>
    {{-- ================================================================
         HERO
    ================================================================ --}}
    <section class="ngn-hero relative overflow-hidden">
        <div class="absolute inset-0 bg-zinc-950 opacity-70 z-10"></div>
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('{{ asset('assets/images/temp/delivery-service-lg.png') }}')"></div>

        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-center"
             style="min-height:70vh">
            <div class="max-w-2xl">
                <span class="inline-block text-orange-400 text-xs font-bold tracking-widest uppercase mb-4">London's Motorbike Specialists</span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                    Ride More.<br>
                    <span class="text-orange-400">Pay Less.</span>
                </h1>
                <p class="text-zinc-300 text-lg mb-8 leading-relaxed">
                    Sales, rental, servicing and recovery. East London's trusted motorcycle dealership since 2015.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('v2.bikes.sale') }}" class="btn-ngn text-sm px-6 py-3">
                        View Bikes for Sale
                    </a>
                    <a href="{{ route('v2.rentals') }}" class="btn-ngn-outline text-sm px-6 py-3">
                        Rent a Motorbike
                    </a>
                </div>
            </div>
        </div>

        {{-- Floating stats bar --}}
        <div class="absolute bottom-0 left-0 right-0 z-20">
            <div class="bg-orange-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap gap-6 justify-center sm:justify-between text-sm font-semibold">
                    <span>&#9650; 500+ Bikes Sold</span>
                    <span>&#9650; 1,000+ Happy Riders</span>
                    <span>&#9650; Same-Day Recovery</span>
                    <span>&#9650; 5&#9733; Trustpilot Rating</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         SERVICES GRID
    ================================================================ --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-orange-600 text-xs font-bold tracking-widest uppercase">What We Do</span>
                <h2 class="ngn-section-title mt-2">Everything You Need,<br class="hidden sm:block"> Under One Roof</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('v2.bikes.sale') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M12 2a10 10 0 100 20A10 10 0 0012 2zm0 0v4m0 16v-4M2 12h4m16 0h-4"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Bikes for Sale</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">New and used motorcycles at competitive prices.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">Browse Stock &rarr;</span>
                </a>

                <a href="{{ route('v2.rentals') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Daily Rentals</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Flexible daily, weekly and monthly rental plans.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">Rent Now &rarr;</span>
                </a>

                <a href="{{ route('v2.services') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Servicing</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Expert maintenance from qualified technicians.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">Book a Service &rarr;</span>
                </a>

                <a href="{{ route('v2.recovery') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Recovery</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">24/7 breakdown and recovery across London.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">Get Help Now &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ================================================================
         FEATURED BIKES
    ================================================================ --}}
    @if($featuredBikes->isNotEmpty())
    <section class="py-16 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="text-orange-600 text-xs font-bold tracking-widest uppercase">Available Now</span>
                    <h2 class="ngn-section-title mt-1">Latest Stock</h2>
                </div>
                <a href="{{ route('v2.bikes.sale') }}" class="btn-ngn-outline text-xs px-4 py-2 hidden sm:inline-flex">View All Bikes</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredBikes as $sale)
                    @php
                        $mb = $sale->getMotorbike();
                        $img = $sale->getMotorbikeImage();
                    @endphp
                    @if($mb)
                    <a href="{{ route('v2.bike.detail', $mb->slug ?? $mb->id) }}" class="ngn-bike-card group">
                        <div class="ngn-bike-card-img overflow-hidden bg-zinc-200">
                            @if($img)
                                <img src="{{ Storage::url($img->image_path ?? '') }}"
                                     alt="{{ $mb->make }} {{ $mb->model }}"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="w-full h-48 flex items-center justify-center bg-zinc-800 text-zinc-500 text-sm">No image</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-bold text-zinc-900 text-base leading-tight">{{ $mb->make }} {{ $mb->model }}</h3>
                                @if($sale->condition)
                                    <span class="ngn-badge-{{ $sale->condition === 'new' ? 'orange' : 'zinc' }} flex-shrink-0">{{ ucfirst($sale->condition) }}</span>
                                @endif
                            </div>
                            <p class="text-zinc-500 text-xs mb-3">{{ $mb->year }} &bull; {{ $mb->engine }}cc &bull; {{ $mb->color }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-orange-600 font-black text-xl">&pound;{{ number_format($sale->price) }}</span>
                                <span class="text-xs text-orange-600 font-semibold">View Details &rarr;</span>
                            </div>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>

            <div class="text-center mt-8 sm:hidden">
                <a href="{{ route('v2.bikes.sale') }}" class="btn-ngn text-sm px-6 py-3">View All Bikes</a>
            </div>
        </div>
    </section>
    @endif

    {{-- ================================================================
         WHY NGN
    ================================================================ --}}
    <section class="py-16 bg-zinc-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-orange-400 text-xs font-bold tracking-widest uppercase">Why Choose Us</span>
                <h2 class="text-3xl font-black mt-2">Built for Riders, by Riders</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">01</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">Expert Team</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">Our qualified technicians have decades of combined experience across all makes and models.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">02</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">Transparent Pricing</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">No hidden fees. You'll always know exactly what you're paying before any work begins.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">03</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">East London Based</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">Conveniently located in Ilford — easy access from the A12 and A406.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">04</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">Finance Available</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">Flexible finance options to get you on the road without the upfront cost.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">05</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">24/7 Recovery</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">Broken down anywhere in London? Our recovery team is on standby around the clock.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white font-black text-lg">06</div>
                    <div>
                        <h4 class="font-bold text-white mb-1">NGN Club Rewards</h4>
                        <p class="text-zinc-400 text-sm leading-relaxed">Join our loyalty club for exclusive discounts, priority bookings and member benefits.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         RENTAL CTA
    ================================================================ --}}
    <section class="py-16 bg-orange-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-3xl font-black leading-tight mb-3">Need a Bike Today?</h2>
                <p class="text-orange-100 text-lg max-w-xl">Scan a QR code at our showroom or book online in minutes. No long queues, no paperwork headaches.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn-dark text-sm px-6 py-3">
                    Book Online Now
                </a>
                <a href="{{ route('v2.rentals') }}" class="inline-flex items-center gap-2 border-2 border-white text-white hover:bg-white hover:text-orange-600 font-semibold px-6 py-3 text-sm transition-colors duration-150">
                    View Rental Fleet
                </a>
            </div>
        </div>
    </section>

    {{-- ================================================================
         MOT CHECKER CTA
    ================================================================ --}}
    <section class="py-14 bg-white border-t border-zinc-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-orange-600 text-xs font-bold tracking-widest uppercase">Free Tool</span>
            <h2 class="ngn-section-title mt-2 mb-4">Check Your MOT Status Instantly</h2>
            <p class="text-zinc-500 mb-8">Enter your number plate — get full MOT history, expiry date and advisory notices for free.</p>
            <a href="{{ route('v2.mot.checker') }}" class="btn-ngn text-sm px-8 py-3">Check MOT Now — It's Free</a>
        </div>
    </section>
</div>
