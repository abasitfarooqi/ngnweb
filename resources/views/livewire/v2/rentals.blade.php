<div>
    {{-- Hero --}}
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-black text-white mb-3">Motorcycle Rentals London</h1>
            <p class="text-zinc-400 text-lg max-w-2xl">Flexible daily, weekly and monthly rental plans. Delivery available across London.</p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn text-sm px-6 py-3">Book Now Online</a>
                <a href="tel:+447907600611" class="btn-ngn-dark text-sm px-6 py-3">Call 07907 600 611</a>
            </div>
        </div>
    </div>

    {{-- Pricing plans --}}
    <section class="py-14 bg-zinc-50 border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-zinc-900 mb-8 text-center">Rental Plans</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="ngn-pricing-card">
                    <div class="text-xs font-bold tracking-widest uppercase text-orange-600 mb-2">Daily</div>
                    <div class="text-4xl font-black text-zinc-900 mb-1">From &pound;35<span class="text-base font-normal text-zinc-500">/day</span></div>
                    <p class="text-zinc-500 text-sm mb-4">Perfect for short trips, deliveries or trying a new bike.</p>
                    <ul class="space-y-2 text-sm text-zinc-600 mb-6">
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Helmet included</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Insurance available</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Pick-up from Ilford</li>
                    </ul>
                    <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn w-full justify-center text-sm">Book Daily</a>
                </div>
                <div class="ngn-pricing-card border-2 border-orange-500 relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-orange-600 text-white text-xs font-bold px-3 py-1">MOST POPULAR</div>
                    <div class="text-xs font-bold tracking-widest uppercase text-orange-600 mb-2">Weekly</div>
                    <div class="text-4xl font-black text-zinc-900 mb-1">From &pound;180<span class="text-base font-normal text-zinc-500">/week</span></div>
                    <p class="text-zinc-500 text-sm mb-4">Great value for regular commuters or extended use.</p>
                    <ul class="space-y-2 text-sm text-zinc-600 mb-6">
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Helmet included</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Insurance available</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> London delivery option</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Priority support</li>
                    </ul>
                    <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn w-full justify-center text-sm">Book Weekly</a>
                </div>
                <div class="ngn-pricing-card">
                    <div class="text-xs font-bold tracking-widest uppercase text-orange-600 mb-2">Monthly</div>
                    <div class="text-4xl font-black text-zinc-900 mb-1">From &pound;550<span class="text-base font-normal text-zinc-500">/month</span></div>
                    <p class="text-zinc-500 text-sm mb-4">Full flexibility with our best per-day rate.</p>
                    <ul class="space-y-2 text-sm text-zinc-600 mb-6">
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Helmet included</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Insurance available</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> London delivery option</li>
                        <li class="flex gap-2"><span class="text-orange-500">&#10003;</span> Free swap on fault</li>
                    </ul>
                    <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn w-full justify-center text-sm">Book Monthly</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Available fleet --}}
    <section class="py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-zinc-900 mb-8">Available Fleet</h2>
            @if($bikes->isEmpty())
                <p class="text-zinc-500 text-center py-12">Fleet details coming soon. Call us on <a href="tel:+447907600611" class="text-orange-600 font-semibold">07907 600 611</a> for availability.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($bikes as $bike)
                    <a href="{{ route('v2.rental.detail', $bike->id) }}" class="ngn-bike-card group">
                        <div class="overflow-hidden bg-zinc-100" style="height:180px">
                            <div class="w-full h-full flex items-center justify-center bg-zinc-800 text-zinc-500 text-sm">{{ $bike->make }} {{ $bike->model }}</div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm text-zinc-900">{{ $bike->make }} {{ $bike->model }}</h3>
                            <p class="text-zinc-500 text-xs">{{ $bike->year }} &bull; {{ $bike->engine }}cc</p>
                            <span class="mt-2 inline-flex text-xs text-orange-600 font-semibold">View &rarr;</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- QR Booking CTA --}}
    <section class="py-14 bg-zinc-900 text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-black mb-4">QR Rental Booking</h2>
            <p class="text-zinc-400 mb-6">Walk into our showroom, scan the QR code on any available bike and complete your booking in under 2 minutes — no waiting, no paperwork.</p>
            <a href="{{ route('v2.rental.qr.booking') }}" class="btn-ngn text-sm px-8 py-3">Start Booking Now</a>
        </div>
    </section>
</div>
