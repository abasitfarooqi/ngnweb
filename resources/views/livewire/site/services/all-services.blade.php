@php
    $sections = [
        [
            'panel' => 'Repairs',
            'slug' => 'repairs',
            'icon' => 'wrench-screwdriver',
            'title' => 'Motorcycle Repairs',
            'image' => 'assets/images/services/repairs.jpg',
            'alt' => 'Motorcycle repairs and workshop — NGN London',
            'teaser' => 'Expert repairs, maintenance and MOT in South London for Japanese and European bikes — reliable workmanship and competitive pricing.',
            'buttons' => [
                ['href' => route('site.repairs.full'), 'label' => 'Full service details', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => 'tel:02083141498', 'label' => 'Call now', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
                ['href' => route('all-services', ['service' => 'Repairs']).'#service-enquiry', 'label' => 'Enquire', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'MOT',
            'slug' => 'mot',
            'icon' => 'shield-check',
            'title' => 'MOT Services',
            'image' => 'assets/images/services/MOT-BOOKING.jpg',
            'alt' => 'Motorcycle MOT testing at NGN',
            'teaser' => 'Qualified testers and mechanics at our South London workshop — your bike inspected to current standards with clear results.',
            'buttons' => [
                ['href' => route('site.mot'), 'label' => 'MOT information', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => 'tel:02083141498', 'label' => 'Call now', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
                ['href' => route('all-services', ['service' => 'MOT']).'#service-enquiry', 'label' => 'Enquire', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'BookService',
            'slug' => 'servicing',
            'icon' => 'cog-6-tooth',
            'title' => 'Servicing & Maintenance',
            'image' => 'assets/images/services/full-service.jpg',
            'alt' => 'Motorcycle servicing and maintenance',
            'teaser' => 'Basic and full packages covering brakes, steering, suspension, tyres and more — compare options and book with our technicians.',
            'buttons' => [
                ['href' => route('site.repairs.basic'), 'label' => 'Basic service', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => route('site.repairs.full'), 'label' => 'Full service', 'variant' => 'filled', 'class' => 'bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700'],
                ['href' => route('site.repairs.comparison'), 'label' => 'Compare packages', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
                ['href' => route('all-services', ['service' => 'BookService', 'booking' => 'Motorcycle Basic Service Enquiry']).'#service-enquiry', 'label' => 'Enquire — basic', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
                ['href' => route('all-services', ['service' => 'BookService', 'booking' => 'Motorcycle Full Service Enquiry']).'#service-enquiry', 'label' => 'Enquire — full', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'Delivery',
            'slug' => 'delivery',
            'icon' => 'truck',
            'title' => 'Delivery & Recovery',
            'image' => 'assets/images/wide-motorbike-recovery.jpg',
            'alt' => 'Motorcycle delivery and recovery',
            'teaser' => 'Secure transport for private and commercial customers. Free collection with our repair service. Nationwide delivery from £249.99 in England.',
            'buttons' => [
                ['href' => route('motorcycle.delivery'), 'label' => 'Delivery & recovery', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => 'tel:02083141498', 'label' => 'Call now', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'Sales',
            'slug' => 'sales',
            'icon' => 'shopping-bag',
            'title' => 'Motorcycle Sales',
            'image' => 'assets/images/services/new-and-used-motorcycles-for-sale-in-london.png',
            'alt' => 'New and used motorcycles for sale in London',
            'teaser' => 'New and used Honda, Yamaha and more — 2025 range, flexible finance on new stock, and practical used options for London riders.',
            'buttons' => [
                ['href' => route('motorcycles.new'), 'label' => 'New motorcycles', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => route('motorcycles.used'), 'label' => 'Used motorcycles', 'variant' => 'filled', 'class' => 'bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700'],
                ['href' => 'tel:02083141498', 'label' => 'Call now', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'Rental',
            'slug' => 'rental',
            'icon' => 'calendar-days',
            'title' => 'Motorcycle Rental',
            'image' => 'assets/images/services/motorcycle-rental-hire-london.jpg',
            'alt' => 'Motorcycle rental in London',
            'teaser' => 'Rent across London, Tooting, Sutton and Catford — from £70 per week. Ideal for commuters and delivery riders.',
            'buttons' => [
                ['href' => route('site.rentals'), 'label' => 'Rental fleet', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => 'tel:02083141498', 'label' => 'Call now', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
                ['href' => route('all-services', ['service' => 'Rental']).'#service-enquiry', 'label' => 'Enquire', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'Accident',
            'slug' => 'accident',
            'icon' => 'exclamation-triangle',
            'title' => 'Accident Management',
            'image' => 'assets/images/services/accident.jpg',
            'alt' => 'Accident management and claims support',
            'teaser' => 'Support after road traffic accidents — experienced team to guide you through the process.',
            'buttons' => [
                ['href' => route('accident-management'), 'label' => 'Make a claim', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
                ['href' => route('all-services', ['service' => 'Accident']).'#service-enquiry', 'label' => 'Enquire', 'variant' => 'outline', 'class' => 'border-slate-300 dark:border-gray-600'],
            ],
        ],
        [
            'panel' => 'Finance',
            'slug' => 'finance',
            'icon' => 'banknotes',
            'title' => 'Finance',
            'image' => 'assets/images/services/finance.jpg',
            'alt' => 'Motorcycle finance options',
            'teaser' => 'Finance options to make your next motorcycle purchase straightforward — ask us what is available on new stock.',
            'buttons' => [
                ['href' => route('site.finance'), 'label' => 'Finance overview', 'variant' => 'filled', 'class' => 'bg-brand-red text-white hover:bg-brand-red-dark'],
            ],
        ],
    ];
@endphp

<div
    x-data="{
        map: @js(array_column($sections, 'slug', 'panel')),
        scrollFromQuery() {
            const q = new URLSearchParams(window.location.search).get('service');
            if (!q || !this.map[q]) return;
            this.$nextTick(() => {
                document.getElementById('svc-' + this.map[q])?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    }"
    x-init="scrollFromQuery(); window.addEventListener('livewire:navigated', () => setTimeout(() => $data.scrollFromQuery(), 120))"
>
    {{-- Hero — same pattern as resources/views/livewire/site/repairs/index.blade.php --}}
    <div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Our Services</h1>
            <nav class="text-sm text-gray-400" aria-label="Breadcrumb">
                <ol class="flex flex-wrap gap-2 list-none p-0 m-0 font-semibold">
                    <li><a href="{{ route('site.home') }}" class="hover:text-white underline-offset-2">Home Page</a></li>
                    <li aria-hidden="true">/</li>
                    <li><span class="text-gray-300">Our Services</span></li>
                </ol>
            </nav>
            <p class="mt-4 text-sm md:text-base text-gray-300 max-w-3xl leading-relaxed">Everything NGN offers in one place — repairs, MOT, servicing, delivery, sales, rental, accident support and finance. Use the cards below, then the universal enquiry form at the bottom.</p>
            <div class="mt-6 flex flex-wrap gap-2 justify-start">
                <flux:button href="{{ route('site.repairs') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Repairs hub</flux:button>
                <flux:button href="{{ route('site.repairs.comparison') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Compare servicing</flux:button>
                <flux:button href="{{ route('site.mot') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">MOT</flux:button>
                <flux:button href="{{ route('site.contact.service-booking') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Dedicated booking</flux:button>
            </div>
        </div>
    </div>

    {{-- Body — same shell as repairs/full.blade.php and repair-services.blade.php --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        <flux:callout variant="info" icon="information-circle" class="mb-10">
            <flux:callout.text class="text-sm leading-relaxed">Each card opens the right detail page or enquiry preset. Links with <span class="font-semibold">Enquire</span> set the form at the bottom of this page using your chosen service.</flux:callout.text>
        </flux:callout>

        {{-- Card grid — same as repair-services: md:2 cols, lg:3 cols --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($sections as $s)
                <div id="svc-{{ $s['slug'] }}" class="scroll-mt-28 min-w-0">
                    <flux:card class="flex h-full flex-col overflow-hidden border border-slate-200 bg-white p-0 shadow-sm transition-shadow duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div class="relative aspect-[16/10] w-full shrink-0 overflow-hidden bg-gray-100 dark:bg-gray-900">
                            <img
                                src="{{ asset($s['image']) }}"
                                alt="{{ $s['alt'] }}"
                                class="h-full w-full object-cover object-center"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="mb-3 flex items-start gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center border border-brand-red/20 bg-brand-red/10 text-brand-red">
                                    <flux:icon name="{{ $s['icon'] }}" class="h-6 w-6" />
                                </span>
                                <h2 class="pt-1 text-lg font-bold leading-snug text-gray-900 dark:text-white">{{ $s['title'] }}</h2>
                            </div>
                            <p class="flex-1 text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $s['teaser'] }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($s['buttons'] as $btn)
                                    <flux:button
                                        href="{{ $btn['href'] }}"
                                        variant="{{ $btn['variant'] ?? 'outline' }}"
                                        size="sm"
                                        class="{{ $btn['class'] ?? 'border-slate-300 dark:border-gray-600' }}"
                                    >{{ $btn['label'] }}</flux:button>
                                @endforeach
                            </div>
                        </div>
                    </flux:card>
                </div>
            @endforeach
        </div>

        {{-- Wide links row — same idea as repairs index “why choose” / repair-services additional block --}}
        <flux:card class="mt-12 border border-slate-200 bg-slate-50 p-6 md:p-8 dark:border-gray-700 dark:bg-gray-800/80">
            <h3 class="mb-6 text-center text-xl font-bold text-brand-red">More on the site</h3>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:text-center">
                <ul class="m-0 list-none space-y-2 p-0 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <li><a href="{{ route('site.repairs') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">Repairs hub</a></li>
                    <li><a href="{{ route('site.repairs.repair-services') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">Repair menu</a></li>
                </ul>
                <ul class="m-0 list-none space-y-2 p-0 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <li><a href="{{ route('site.repairs.comparison') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">Compare basic &amp; full</a></li>
                    <li><a href="{{ route('site.mot') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">MOT testing</a></li>
                </ul>
                <ul class="m-0 list-none space-y-2 p-0 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <li><a href="{{ route('site.contact.service-booking') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">Service booking page</a></li>
                    <li><a href="{{ route('sale-motorcycles') }}" class="text-brand-red underline underline-offset-2 hover:text-brand-red-dark">Motorcycle sales</a></li>
                </ul>
            </div>
        </flux:card>

        <x-site.repairs.branches-cta-dark
            heading="Speak to a branch"
            intro="Catford, Tooting and Sutton — call or visit for bookings and advice on any service above."
        />

        {{-- Enquiry — same width treatment as repairs/index enquiry block --}}
        <div id="service-enquiry" class="mx-auto mt-14 max-w-3xl scroll-mt-28">
            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Universal service enquiry</h2>
            <p class="mb-6 text-sm leading-relaxed text-gray-600 dark:text-gray-400">Choose the service type in the form; it can be preset when you use an Enquire link from a card above.</p>
            <livewire:site.contact.service-booking
                :embedded="true"
                :initial-service-type="$this->bookingPresetForChild()"
                wire:key="all-services-booking-{{ $openPanel }}-{{ $bookingServiceType }}"
            />
        </div>
    </div>
</div>
