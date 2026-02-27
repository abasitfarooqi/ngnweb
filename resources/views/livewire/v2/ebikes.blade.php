<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <span class="inline-block text-orange-400 text-xs font-bold tracking-widest uppercase mb-3">Electric Motorcycles</span>
            <h1 class="text-4xl font-black text-white mb-3">Electric Bikes</h1>
            <p class="text-zinc-400 text-lg max-w-2xl">Zero emissions, zero noise, zero compromise. Discover our range of electric motorcycles and scooters.</p>
        </div>
    </div>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($ebikes->isEmpty())
                <div class="text-center py-20 text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <p class="text-lg font-semibold">Electric bikes coming soon</p>
                    <p class="text-sm mt-1">Contact us to express interest or pre-order.</p>
                    <a href="{{ route('v2.contact') }}" class="btn-ngn text-sm px-6 py-3 mt-6 inline-flex">Contact Us</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($ebikes as $bike)
                    <div class="ngn-bike-card">
                        <div class="bg-zinc-800 flex items-center justify-center text-zinc-500 text-sm" style="height:200px">
                            {{ $bike->make }} {{ $bike->model }}
                        </div>
                        <div class="p-4">
                            <span class="ngn-badge-green mb-2">Electric</span>
                            <h3 class="font-bold text-zinc-900 text-base mt-2">{{ $bike->make }} {{ $bike->model }}</h3>
                            <p class="text-zinc-500 text-xs mt-1">{{ $bike->year }}</p>
                            <a href="{{ route('v2.contact') }}" class="btn-ngn w-full justify-center text-sm mt-4">Enquire</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Why electric --}}
    <section class="py-14 bg-zinc-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-center mb-10">Why Go Electric?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                @foreach([
                    ['Zero Emissions', 'Cleaner air for London — no exhaust fumes.'],
                    ['Lower Running Costs', 'Charge for pennies vs. filling up at the pump.'],
                    ['ULEZ Exempt', 'Free to ride anywhere in the Ultra Low Emission Zone.'],
                    ['Instant Torque', 'Smooth, powerful acceleration from a standstill.'],
                ] as [$title, $desc])
                <div>
                    <div class="w-12 h-12 bg-orange-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-white mb-1">{{ $title }}</h3>
                    <p class="text-zinc-400 text-sm">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
