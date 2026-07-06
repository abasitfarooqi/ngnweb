<section
    id="engine-rebuilds"
    class="relative border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 scroll-mt-16"
    aria-label="Specialist motorcycle engine rebuilds"
>
    <div class="absolute top-0 left-0 right-0 h-1 bg-brand-green" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 shadow-sm dark:shadow-none">
            {{-- Workshop image --}}
            <div class="relative min-h-[200px] sm:min-h-[240px] lg:min-h-0 lg:h-full">
                <img
                    src="{{ asset('images/engine-rebuild-workshop.png') }}"
                    alt="NGN specialist technician with Honda engine rebuild in our London workshop"
                    width="960"
                    height="640"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 h-full w-full object-cover object-center"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-black/25 via-transparent to-transparent lg:from-black/35 lg:via-black/10 lg:to-transparent pointer-events-none" aria-hidden="true"></div>
                <div class="absolute bottom-0 left-0 right-0 hidden sm:block bg-gradient-to-t from-black/80 via-black/50 to-transparent px-4 py-3 pointer-events-none lg:hidden" aria-hidden="true">
                    <p class="text-[11px] leading-snug text-white/90">Honda PCX · Yamaha NMAX · Vespa · SYM · delivery &amp; commuter specialists</p>
                </div>
            </div>

            {{-- Copy + CTA — one column, no extra blocks below --}}
            <div class="flex flex-col justify-center p-6 md:p-8">
                <flux:badge class="site-flux-badge-green mb-2 w-fit uppercase tracking-widest text-[10px]">Workshop specialists</flux:badge>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                    Specialist motorcycle &amp; scooter engine rebuilds
                </h2>
                <p class="mt-1 text-xs font-semibold text-brand-green">Serving South London &amp; Greater London</p>

                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Engine failure, oil consumption, knocking or loss of compression? We provide <strong class="text-gray-900 dark:text-white">complete strip-down, inspection and rebuild</strong> for commuters, delivery fleets and leisure riders — Honda, Yamaha, Suzuki, Kawasaki, Vespa, Piaggio, SYM, Lexmoto and more.
                </p>

                <ul class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 text-xs text-gray-700 dark:text-gray-300 list-none p-0 m-0" role="list">
                    @foreach ([
                        'Full strip-down & inspection',
                        'OEM or premium parts',
                        'Transparent quotations',
                        'Catford, Tooting & Sutton',
                    ] as $point)
                        <li class="flex items-start gap-1.5">
                            <span class="text-brand-green font-bold shrink-0" aria-hidden="true">✓</span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>

                <details class="mt-4 group">
                    <summary class="cursor-pointer text-xs font-semibold text-brand-green hover:text-brand-green-dark list-none flex items-center gap-1 [&::-webkit-details-marker]:hidden">
                        <span>Popular models &amp; full service list</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform" aria-hidden="true">▾</span>
                    </summary>
                    <div class="mt-3 space-y-3 text-xs text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-200 dark:border-gray-700 pt-3">
                        <p>
                            <strong class="text-gray-800 dark:text-gray-200">Models:</strong>
                            Honda PCX 125, SH125i, CB125F, Forza 125; Yamaha NMAX, XMAX, MT-125, YBR125; Suzuki Address &amp; Burgman 125; Kawasaki J125; Piaggio Liberty &amp; Medley; Vespa Primavera &amp; GTS; SYM Jet &amp; Symphony; Lexmoto and many more.
                        </p>
                        <p>
                            <strong class="text-gray-800 dark:text-gray-200">Includes:</strong>
                            engine removal where required, cylinder head &amp; crankshaft inspection, pistons, valves, timing chain, gearbox, clutch, bearings, seals, reassembly to spec, fresh oil/coolant, and final quality check. Only parts needed for a safe rebuild are replaced.
                        </p>
                    </div>
                </details>

                <div id="engine-rebuild-booking" class="mt-5 flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 sm:gap-3">
                    <flux:button
                        href="{{ route('all-services', ['service' => 'MotorcycleEngineRepairs']) }}#service-enquiry"
                        variant="filled"
                        size="sm"
                        class="bg-brand-green text-white hover:bg-brand-green-dark justify-center"
                    >
                        Engine rebuild enquiry
                    </flux:button>
                    <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600 justify-center">
                        Call now
                    </flux:button>
                    <a href="{{ route('all-services', ['service' => 'MotorcycleEngineRepairs']) }}#svc-engine-rebuilds" class="text-xs font-semibold text-brand-green hover:text-brand-green-dark text-center sm:text-left py-2 sm:py-0">
                        All services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
