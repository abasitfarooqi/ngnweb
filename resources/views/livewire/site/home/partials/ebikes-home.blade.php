{{-- E-bikes teaser — light & dark --}}
<section class="relative overflow-hidden border-b border-gray-200 dark:border-gray-800 bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-gray-900 dark:to-slate-950 text-gray-900 dark:text-white" x-data="{ ebikeLightbox: false }" @keydown.escape.window="ebikeLightbox = false">
    <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-10 items-center">
            <div class="md:col-span-4 text-center md:text-left">
                <flux:badge color="red" class="mb-3 uppercase tracking-widest text-[10px]">Electric</flux:badge>
                <button type="button" class="block w-full max-w-sm mx-auto md:mx-0 border-0 p-0 bg-transparent cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900" @click="ebikeLightbox = true" aria-label="Enlarge e-bike banner image">
                    <img loading="lazy" src="{{ asset('assets/images/ebikes-rental-buy-london-banner.jpg') }}" alt="E-bikes in London" class="w-full h-auto object-cover border border-gray-200 dark:border-gray-700 shadow-md dark:shadow-black/40">
                </button>
            </div>
            <div class="md:col-span-8 text-center md:text-left">
                <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-3">E-bikes revolution</h2>
                <p class="text-base md:text-lg font-semibold text-brand-red dark:text-red-400 mb-2">
                    Our first e-bike launches in <strong>6 weeks</strong> — eco-friendly and smart.
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 max-w-xl mx-auto md:mx-0 leading-relaxed mb-6">
                    Pedal-assist models for London riders. Register your interest and browse accessories on our e-bikes page.
                </p>
                <flux:button href="{{ route('site.ebikes') }}" variant="outline" size="sm" class="border-gray-400 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900">
                    View e-bikes
                </flux:button>
            </div>
        </div>
    </div>
    <template x-teleport="body">
        <div
            x-show="ebikeLightbox"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80"
            @click.self="ebikeLightbox = false"
            role="dialog"
            aria-modal="true"
            aria-label="E-bikes banner"
        >
            <div class="relative max-w-5xl w-full max-h-[90vh] overflow-auto bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 shadow-xl">
                <button type="button" class="absolute top-3 right-3 z-10 px-3 py-1 text-sm font-semibold bg-gray-900 text-white dark:bg-white dark:text-gray-900 hover:opacity-90" @click="ebikeLightbox = false">Close</button>
                <div class="p-4 pt-12">
                    <img loading="lazy" src="{{ asset('assets/images/ebikes-rental-buy-london-banner.jpg') }}" alt="E-bikes in London" class="w-full h-auto">
                </div>
            </div>
        </div>
    </template>
</section>
