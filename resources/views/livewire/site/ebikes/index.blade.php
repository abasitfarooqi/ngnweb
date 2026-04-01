<div class="bg-white dark:bg-gray-900">
    @push('head')
        <meta name="keywords" content="e-bikes, pedal-assist, electric bicycles, London, buy e-bike, hire e-bike, eco-friendly transport, urban cycling, EAPC, UK, launch">
    @endpush

    {{-- Gallery: fixed viewport height; image object-contain inside --}}
    <div
        class="relative border-b border-gray-800 bg-gray-950 text-white dark:border-gray-700 dark:bg-black"
        x-data="{
            urls: {{ json_encode($galleryUrls) }},
            i: 0,
            paused: false,
            zoomOpen: false,
            zoomSrc: '',
            init() {
                setInterval(() => {
                    if (!this.paused && !this.zoomOpen) {
                        this.i = (this.i + 1) % this.urls.length;
                    }
                }, 50000);
            },
            prev() { this.i = (this.i - 1 + this.urls.length) % this.urls.length; },
            next() { this.i = (this.i + 1) % this.urls.length; },
            openZoom() { this.zoomSrc = this.urls[this.i]; this.zoomOpen = true; this.paused = true; },
            closeZoom() { this.zoomOpen = false; this.paused = false; },
            requestFs() {
                const el = this.$refs.mainImg;
                if (!el) return;
                if (el.requestFullscreen) el.requestFullscreen();
                else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
            }
        }"
        @keydown.escape.window="closeZoom()"
    >
        <div
            class="relative flex h-[280px] w-full overflow-hidden sm:h-[340px] md:h-[400px] lg:h-[460px]"
            @mouseenter="paused = true"
            @mouseleave="paused = false"
        >
            <div
                class="pointer-events-none absolute inset-y-0 left-0 w-[12%] max-w-[140px] opacity-40"
                aria-hidden="true"
                x-bind:style="`background-image:url('${urls[i]}');background-size:cover;background-position:center;filter:blur(16px);transform:scale(1.06)`"
            ></div>
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-[12%] max-w-[140px] opacity-40"
                aria-hidden="true"
                x-bind:style="`background-image:url('${urls[i]}');background-size:cover;background-position:center;filter:blur(16px);transform:scale(1.06)`"
            ></div>

            <button
                type="button"
                class="absolute left-2 top-1/2 z-10 -translate-y-1/2 border border-white/30 bg-black/60 px-2.5 py-3 text-lg text-white transition hover:bg-black/80 sm:px-3 sm:py-4 sm:text-xl"
                aria-label="Previous slide"
                @click="prev"
            >&#10094;</button>
            <button
                type="button"
                class="absolute right-2 top-1/2 z-10 -translate-y-1/2 border border-white/30 bg-black/60 px-2.5 py-3 text-lg text-white transition hover:bg-black/80 sm:px-3 sm:py-4 sm:text-xl"
                aria-label="Next slide"
                @click="next"
            >&#10095;</button>

            <div class="relative z-[1] flex h-full min-h-0 min-w-0 flex-1 items-center justify-center px-12 py-3 sm:px-16 sm:py-4">
                <img
                    x-ref="mainImg"
                    :src="urls[i]"
                    alt="Pedal-assist e-bike gallery image"
                    class="max-h-full max-w-full cursor-zoom-in object-contain object-center select-none"
                    width="1200"
                    height="800"
                    decoding="async"
                    @click="openZoom()"
                >
            </div>

            <button
                type="button"
                class="absolute bottom-2 right-2 z-10 border border-white/40 bg-black/70 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-white transition hover:bg-black/90 sm:bottom-3 sm:right-3 sm:px-3 sm:py-1.5 sm:text-xs"
                @click="requestFs()"
            >
                Fullscreen
            </button>
        </div>

        <div class="flex justify-center gap-2 overflow-x-auto border-t border-gray-800 bg-gray-900 px-3 py-3 dark:border-gray-700 dark:bg-gray-950">
            <template x-for="(url, idx) in urls" :key="idx">
                <button
                    type="button"
                    class="flex-shrink-0 border-2 bg-black/30 p-0.5 transition-opacity hover:opacity-100 dark:bg-gray-800"
                    :class="i === idx ? 'border-brand-red opacity-100 ring-1 ring-red-400/50 dark:ring-red-500/40' : 'border-transparent opacity-50 dark:opacity-45'"
                    @click="i = idx"
                >
                    <img :src="url" alt="" class="h-14 w-[88px] object-cover sm:h-16 sm:w-[100px]">
                </button>
            </template>
        </div>

        <div
            x-show="zoomOpen"
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Image zoom"
            x-cloak
        >
            <button type="button" class="absolute right-4 top-4 border border-white/40 bg-white/10 px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/20" @click="closeZoom()" aria-label="Close zoom">Close</button>
            <img :src="zoomSrc" alt="Zoomed e-bike image" class="max-h-[100vh] max-w-[100vw] object-contain">
        </div>
    </div>

    <div class="py-12 pb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section
                class="mb-12 border-b border-gray-200 pb-8 text-center transition duration-700 ease-out dark:border-gray-700"
                x-data="{ shown: false }"
                x-intersect.threshold.10="shown = true"
                x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            >
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-brand-red dark:text-red-400">London &middot; EAPC-compliant</p>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white md:text-4xl">Pedal-assist e-bikes revolution in London</h1>
                <p class="mx-auto mt-5 max-w-3xl text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                    Experience the future of urban mobility. Our brand new pedal-assist electric bicycles are now available for purchase and hire in London. Clean, green, and built for British roads.
                </p>
            </section>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section
                class="mb-14 grid gap-10 transition duration-700 ease-out md:grid-cols-2 md:gap-12"
                x-data="{ shown: false }"
                x-intersect.threshold.10="shown = true"
                x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            >
                <div class="border-l-[3px] border-brand-red pl-5 md:pl-6">
                    <h2 class="mb-5 text-xl font-bold text-gray-900 dark:text-white">Why choose our e-bikes?</h2>
                    <ul class="space-y-4 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                        <li><span class="font-semibold text-gray-900 dark:text-white">Eco-friendly:</span> Zero emissions, perfect for London&rsquo;s ULEZ and clean air zones.</li>
                        <li><span class="font-semibold text-gray-900 dark:text-white">Affordable:</span> Competitive pricing for both sales and rentals. Save on fuel and public transport.</li>
                        <li><span class="font-semibold text-gray-900 dark:text-white">Pedal-assist technology:</span> Enjoy a natural cycling experience with electric assistance up to 15.5 mph, compliant with UK EAPC regulations.</li>
                        <li><span class="font-semibold text-gray-900 dark:text-white">British support:</span> Local service, warranty, and expert advice from our London team.</li>
                        <li><span class="font-semibold text-gray-900 dark:text-white">Perfect for delivery &amp; commuting:</span> Ideal for couriers, city workers, and leisure cyclists.</li>
                    </ul>
                </div>
                <div class="flex flex-col border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:shadow-none">
                    <div class="border-b border-gray-200 bg-white p-6 dark:border-gray-600 dark:bg-gray-800">
                        <h2 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Ready to ride?</h2>
                        <p class="mb-5 text-base leading-relaxed text-gray-600 dark:text-gray-300">
                            Be among the first to experience the pedal-assist e-bike revolution in London. Whether you want to buy or hire, we have the right option for you.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <flux:button href="#ebike-enquiry-form" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                                Enquire now
                            </flux:button>
                            <flux:button href="{{ route('site.contact') }}" variant="outline" class="border-gray-300 text-gray-800 hover:bg-gray-100 dark:border-gray-500 dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                Contact us
                            </flux:button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 divide-y divide-gray-200 dark:divide-gray-600 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                        <div class="bg-white p-5 dark:bg-gray-900">
                            <div class="mb-2 flex h-9 w-9 items-center justify-center bg-brand-red/10 text-brand-red dark:bg-red-950/50 dark:text-red-300">
                                <flux:icon name="globe-europe-africa" class="h-5 w-5" />
                            </div>
                            <h3 class="mb-2 font-bold text-gray-900 dark:text-white">Cleaner air</h3>
                            <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">Help reduce pollution and make London greener for everyone.</p>
                        </div>
                        <div class="bg-white p-5 dark:bg-gray-900">
                            <div class="mb-2 flex h-9 w-9 items-center justify-center bg-brand-red/10 text-brand-red dark:bg-red-950/50 dark:text-red-300">
                                <flux:icon name="banknotes" class="h-5 w-5" />
                            </div>
                            <h3 class="mb-2 font-bold text-gray-900 dark:text-white">Save money</h3>
                            <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">Lower running costs, no congestion charge, and minimal maintenance.</p>
                        </div>
                        <div class="bg-white p-5 dark:bg-gray-900">
                            <div class="mb-2 flex h-9 w-9 items-center justify-center bg-brand-red/10 text-brand-red dark:bg-red-950/50 dark:text-red-300">
                                <flux:icon name="bolt" class="h-5 w-5" />
                            </div>
                            <h3 class="mb-2 font-bold text-gray-900 dark:text-white">Effortless travel</h3>
                            <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">Beat the traffic and arrive fresh, every time.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section
            class="border-t border-gray-200 bg-gray-50 py-12 transition duration-700 ease-out dark:border-gray-700 dark:bg-gray-950"
            x-data="{ shown: false }"
            x-intersect.threshold.10="shown = true"
            x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="mb-2 text-center text-2xl font-bold text-gray-900 dark:text-white">eBike specifications</h2>
                <p class="mb-8 text-center text-sm text-gray-600 dark:text-gray-400">Technical summary for our pedal-assist range</p>
                @include('livewire.site.partials.ebikes.specifications-table')
            </div>
        </section>
    </div>

    <section class="scroll-mt-32 border-t border-gray-200 bg-gray-100 py-14 dark:border-gray-700 dark:bg-gray-950" id="ebike-enquiry-form">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-14">
                <div>
                    <h2 class="mb-2 text-center text-xl font-bold text-gray-900 dark:text-white lg:text-left">Frequently asked questions</h2>
                    <p class="mb-6 text-center text-sm text-gray-600 dark:text-gray-400 lg:text-left">Common questions about UK e-bikes and how we can help</p>

                    <flux:accordion class="border border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-800 [&_button[data-flux-accordion-heading]]:text-gray-900 [&_button[data-flux-accordion-heading]]:dark:text-gray-100 [&_[data-flux-accordion-content]>div]:!text-gray-600 [&_[data-flux-accordion-content]>div]:dark:!text-gray-300">
                        <flux:accordion.item
                            class="!border-b border-gray-200 px-3 dark:!border-gray-600"
                            heading="Do I need a licence to ride a pedal-assist e-bike in the UK?"
                            :expanded="true"
                            :transition="true"
                        >
                            <p class="leading-relaxed text-gray-600 dark:text-gray-300">
                                No, as long as the e-bike meets UK EAPC regulations (maximum 15.5 mph assisted speed, 250W pedal-assist motor, pedals must be used), you do not need a licence, insurance, or tax. Our e-bikes are fully compliant.
                            </p>
                        </flux:accordion.item>
                        <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" :transition="true" heading="Can I use your pedal-assist e-bikes for delivery work?">
                            <p class="leading-relaxed text-gray-600 dark:text-gray-300">
                                Absolutely! Our e-bikes are designed for reliability and range, making them ideal for couriers and delivery riders in London.
                            </p>
                        </flux:accordion.item>
                        <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" :transition="true" heading="Do you offer finance or instalment plans for electric bicycles?">
                            <p class="leading-relaxed text-gray-600 dark:text-gray-300">
                                Yes, flexible finance and instalment options are available. Contact us for details.
                            </p>
                        </flux:accordion.item>
                        <flux:accordion.item class="px-3 !pb-5" :transition="true" heading="Where can I see or test ride the pedal-assist e-bikes?">
                            <p class="leading-relaxed text-gray-600 dark:text-gray-300">
                                Visit our London showrooms or book a test ride online. We&rsquo;re happy to help you find the perfect e-bike.
                            </p>
                        </flux:accordion.item>
                    </flux:accordion>
                </div>

                <div class="lg:pl-2">
                    @include('livewire.site.partials.sales.enquiry-form', [
                        'submitAction' => 'submitEnquiry',
                        'heading' => 'Enquire about e-bikes',
                        'enquiryTypeLabel' => 'Pedal-assist e-bike',
                        'showRegNo' => true,
                        'submitButtonLabel' => 'Enquire now',
                    ])
                </div>
            </div>
        </div>
    </section>

    <div class="bg-brand-red py-10 text-white">
        <div class="max-w-7xl mx-auto flex flex-col items-center justify-between gap-4 px-4 sm:px-6 lg:flex-row lg:px-8">
            <p class="text-center text-lg font-semibold leading-snug text-white lg:text-left">Ready to ride? Send an e-bike enquiry and we&rsquo;ll get back to you.</p>
            <flux:button href="#ebike-enquiry-form" variant="filled" class="shrink-0 bg-white font-semibold text-brand-red hover:bg-gray-100">
                Enquire now
            </flux:button>
        </div>
    </div>
</div>
