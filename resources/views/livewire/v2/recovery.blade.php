<div>
    {{-- Emergency strip --}}
    <div class="bg-red-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm font-semibold">
            <span>&#9888; Broken down right now?</span>
            <a href="tel:+447907600611" class="flex items-center gap-2 bg-white text-red-600 px-4 py-2 font-black text-sm">
                Call 07907 600 611 — Available Now
            </a>
        </div>
    </div>

    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-black text-white mb-3">Motorbike Recovery London</h1>
            <p class="text-zinc-400 text-lg max-w-2xl">24/7 breakdown recovery across Greater London. Fast, reliable and fully insured.</p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="tel:+447907600611" class="btn-ngn text-sm px-6 py-3">Call for Immediate Recovery</a>
                <a href="{{ route('motorbike.recovery.order') }}" class="btn-ngn-dark text-sm px-6 py-3">Submit Recovery Request</a>
            </div>
        </div>
    </div>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="ngn-section-title">Our Recovery Services</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['Roadside Recovery', 'On-the-spot diagnosis and repair where possible. If your bike can be fixed at the roadside, we'll do it.'],
                    ['Tow to Workshop', 'Safe transport of your bike to our Ilford workshop for full assessment and repair.'],
                    ['Accident Recovery', 'Post-accident recovery including damage assessment, insurance support and secure storage.'],
                    ['Tyre Change', 'Emergency tyre replacement on the roadside — we carry common sizes.'],
                    ['Fuel Delivery', 'Run out of fuel? We'll bring enough to get you to the nearest station.'],
                    ['Battery Jump-Start', 'Dead battery? Quick jump-start service to get you back on the road fast.'],
                ] as [$title, $desc])
                <div class="ngn-service-card">
                    <h3 class="font-bold text-lg text-zinc-900 mb-2">{{ $title }}</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-14 bg-zinc-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-black text-orange-400 mb-2">24/7</div>
                <p class="text-zinc-400 text-sm">Available around the clock, including bank holidays</p>
            </div>
            <div>
                <div class="text-4xl font-black text-orange-400 mb-2">&lt;60 min</div>
                <p class="text-zinc-400 text-sm">Average response time within the M25</p>
            </div>
            <div>
                <div class="text-4xl font-black text-orange-400 mb-2">1,000+</div>
                <p class="text-zinc-400 text-sm">Successful recoveries completed</p>
            </div>
        </div>
    </section>
</div>
