<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="text-4xl font-black text-white mb-3">Motorbike Servicing & Maintenance</h1>
            <p class="text-zinc-400 text-lg max-w-2xl">Expert servicing and repairs for all makes and models at our East London workshop.</p>
            <a href="{{ route('v2.service.booking') }}" class="btn-ngn text-sm px-6 py-3 mt-6 inline-flex">Book a Service</a>
        </div>
    </div>

    {{-- Service types grid --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="ngn-section-title">Our Service Packages</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('v2.service.basic') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Basic Service</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Oil change, filter replacement, basic safety check and chain adjustment.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">From &pound;89 &rarr;</span>
                </a>

                <a href="{{ route('v2.service.full') }}" class="ngn-service-card group border-2 border-orange-200">
                    <div class="text-xs font-bold text-orange-600 tracking-widest uppercase mb-2">Recommended</div>
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Full Service</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Comprehensive inspection, all fluids, brakes, tyres, electrics and full safety report.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">From &pound;149 &rarr;</span>
                </a>

                <a href="{{ route('v2.service.repairs') }}" class="ngn-service-card group">
                    <div class="ngn-service-icon bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mt-4 mb-2">Repairs</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Diagnostics, engine work, electrical faults, body repairs and more.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-orange-600 text-sm font-semibold">Get a Quote &rarr;</span>
                </a>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('v2.service.comparison') }}" class="text-orange-600 text-sm font-semibold hover:underline">Compare all service packages &rarr;</a>
            </div>
        </div>
    </section>

    {{-- Workshop info --}}
    <section class="py-14 bg-zinc-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-orange-400 text-xs font-bold tracking-widest uppercase">Our Workshop</span>
                <h2 class="text-3xl font-black mt-2 mb-4">State-of-the-Art Facilities</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">Our fully equipped workshop in Ilford handles everything from routine maintenance to complex engine rebuilds. All work is carried out by qualified technicians using genuine or OEM parts.</p>
                <ul class="space-y-3 text-sm text-zinc-300">
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white text-xs">&#10003;</span> All makes and models accepted</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white text-xs">&#10003;</span> Genuine and OEM parts used</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white text-xs">&#10003;</span> No-fix, no-fee diagnostics</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-orange-600 flex items-center justify-center flex-shrink-0 text-white text-xs">&#10003;</span> Same-day service available</li>
                </ul>
            </div>
            <div class="bg-zinc-800 border border-zinc-700 p-8">
                <h3 class="font-black text-xl mb-4">Opening Hours</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-zinc-700 pb-2"><dt class="text-zinc-400">Monday – Friday</dt><dd class="font-semibold">9:00am – 6:00pm</dd></div>
                    <div class="flex justify-between border-b border-zinc-700 pb-2"><dt class="text-zinc-400">Saturday</dt><dd class="font-semibold">10:00am – 4:00pm</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">Sunday</dt><dd class="text-zinc-500">Closed</dd></div>
                </dl>
                <div class="mt-6">
                    <a href="{{ route('v2.service.booking') }}" class="btn-ngn w-full justify-center text-sm">Book Your Service</a>
                </div>
            </div>
        </div>
    </section>
</div>
