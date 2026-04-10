<div
    x-data="{
        q: '',
        scrollToEnquiry() {
            document.getElementById('navixai-enquiry')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        match(text) {
            return this.q.trim() === '' || text.toLowerCase().includes(this.q.toLowerCase());
        }
    }"
>
    <section class="bg-gray-950 text-white py-14 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs tracking-[0.2em] uppercase text-gray-400">Test page</p>
            <h1 class="mt-2 text-3xl md:text-5xl font-bold">NavixAI One-Page Service Hub</h1>
            <p class="mt-4 text-sm md:text-base text-gray-300 max-w-4xl leading-relaxed">
                This page is isolated for testing. Existing site pages remain unchanged. Club member pages, shop and ecommerce flows are intentionally excluded.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <flux:button href="{{ route('site.home') }}" variant="outline" class="border-white/30 text-white hover:bg-white/10 rounded-none">Main site home</flux:button>
                <flux:button type="button" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark rounded-none" x-on:click="scrollToEnquiry()">Jump to enquiry form</flux:button>
            </div>
        </div>
    </section>

    <section class="py-8 border-y border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2" for="service-search">
                Quick service search
            </label>
            <input
                id="service-search"
                x-model.debounce.200ms="q"
                type="text"
                class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/50 rounded-none"
                placeholder="Type repairs, MOT, rental, finance..."
            >
        </div>
    </section>

    <section navixai="True" data-navixai-block="about-ngn" class="py-12 bg-gray-50 dark:bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">About us context</h2>
            <div class="mt-4 space-y-3 text-sm leading-relaxed text-gray-700 dark:text-gray-300 max-w-5xl">
                <p>Neguinho Motors Limited (NGN) was incorporated in 2018 and operates motorcycle services across London.</p>
                <p>Current core locations are Catford, Tooting and Sutton. Services include rentals, MOT, workshop repairs, servicing, recovery, delivery, finance and motorcycle sales support.</p>
                <p>This marked block is intentionally machine-readable for your NavixAI context loader.</p>
            </div>
        </div>
    </section>

    <section navixai="True" data-navixai-block="services-catalogue" class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">All services on one page</h2>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 max-w-4xl">
                Source links point to existing live pages and flows. Each card can also preselect the enquiry form below.
            </p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($serviceCards as $service)
                    <article
                        x-show="match(@js(strtolower($service['title'].' '.$service['description'])))"
                        class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 flex flex-col gap-4 rounded-none"
                    >
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $service['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $service['description'] }}</p>
                        </div>
                        <div class="mt-auto flex flex-wrap gap-2">
                            <flux:button href="{{ $service['href'] }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600 rounded-none">
                                {{ $service['cta'] }}
                            </flux:button>
                            @if ($service['booking_type'])
                                <flux:button
                                    type="button"
                                    variant="filled"
                                    size="sm"
                                    class="bg-brand-red text-white hover:bg-brand-red-dark rounded-none"
                                    wire:click="setBookingServiceType('{{ $service['booking_type'] }}')"
                                    x-on:click="scrollToEnquiry()"
                                >
                                    Enquire for this
                                </flux:button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section navixai="True" data-navixai-block="branch-context" class="py-12 bg-gray-50 dark:bg-gray-900/40 border-y border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Branch context</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($branches as $branch)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 rounded-none">
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $branch['name'] }}</h3>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $branch['address'] ?: 'Address available on locations page.' }}</p>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $branch['phone'] !== '' ? $branch['phone'] : 'Phone available on contact page.' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="navixai-enquiry" class="py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Universal enquiry form</h2>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                Uses the existing NGN service booking submission flow. Selecting “Enquire for this” on any service card preloads this form.
            </p>

            <div class="mt-8">
                <livewire:site.contact.service-booking
                    :embedded="true"
                    :initial-service-type="$bookingServiceType"
                    wire:key="navixai-service-lab-booking-{{ $bookingServiceType }}"
                />
            </div>
        </div>
    </section>
</div>
