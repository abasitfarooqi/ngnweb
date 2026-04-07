<section class="py-12 md:py-16 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-12 max-w-3xl mx-auto">
            <flux:badge color="red" class="mb-3 uppercase tracking-widest text-[10px]">Workshop</flux:badge>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Extensive motorcycle services</h2>
            <p class="mt-3 text-sm md:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                Servicing, repairs and MOT across Catford, Tooting and Sutton. Same depth as our legacy service pages — compare packages, book enquiries, or browse every workshop offering on <a href="{{ route('all-services') }}" class="text-brand-red font-semibold underline underline-offset-2">all services</a>.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6">
            @foreach ([
                ['img' => 'img/basic-service.jpg', 'title' => 'Basic service', 'sub' => 'Essential maintenance every ~6,000 miles.', 'bullets' => ['Engine: oil & filter', 'Brakes: check, fluid, operation test', 'Tyres: pressure & tread', 'Chain: clean, lube, tension', 'Electrical: lights, battery, horn', 'General: fasteners, leaks, damage'], 'learn' => route('site.repairs.basic')],
                ['img' => 'img/full-services.jpg', 'title' => 'Full service', 'sub' => 'Complete care ~12,000 miles — major package.', 'bullets' => ['Everything in basic, plus deeper engine & filters', 'Transmission: gearbox oil, clutch', 'Brakes: pads/discs, fluid replace, calipers', 'Suspension, cooling, exhaust, frame checks', 'Optional test ride & software updates'], 'learn' => route('site.repairs.full')],
                ['img' => 'img/motorbike-repair-workshop-london.jpg', 'title' => 'Repairs', 'sub' => 'Fault-finding and fix — full repair menu.', 'bullets' => ['Diagnosis', 'Chains & sprockets', 'Brake system work', 'Tyres & punctures', 'Forks, steering bearings', 'Performance parts & insurance repairs'], 'learn' => route('site.repairs.repair-services')],
                ['img' => 'assets/images/services/MOT-BOOKING.jpg', 'title' => 'MOT', 'sub' => 'Expert motorcycle MOT.', 'bullets' => ['Qualified testers', 'Current inspection standards', 'Book online or by phone'], 'learn' => route('site.mot'), 'enquiry' => route('site.contact.service-booking', ['service' => 'MOT Booking Enquiry'])],
            ] as $card)
                <div class="flex flex-col h-full min-w-0">
                    <flux:card class="flex-1 flex flex-col overflow-hidden p-0 border-0 ring-1 ring-gray-200/90 dark:ring-gray-700 bg-gray-50 dark:bg-gray-900 shadow-md shadow-gray-900/5 dark:shadow-none hover:ring-brand-red/35 transition-all duration-300">
                        <div class="relative w-full h-[150px] shrink-0 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                            <img loading="lazy" src="{{ asset($card['img']) }}" alt="{{ $card['title'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $card['title'] }}</h3>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-1">{{ $card['sub'] }}</p>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 mt-3 space-y-1.5 leading-relaxed list-disc list-inside marker:text-brand-red">
                                @foreach ($card['bullets'] as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                            @if (!empty($card['enquiry']))
                                <flux:button href="{{ $card['enquiry'] }}" variant="outline" size="sm" class="mt-4 w-full justify-center ring-1 ring-gray-300 dark:ring-gray-600">
                                    MOT enquiry
                                </flux:button>
                            @endif
                        </div>
                    </flux:card>
                    <div class="grid grid-cols-1 gap-2 mt-3">
                        <flux:button href="{{ $card['learn'] }}" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark justify-center">
                            Learn more
                        </flux:button>
                        <flux:button href="{{ route('site.repairs.comparison') }}" variant="outline" size="sm" class="w-full justify-center border-gray-300 dark:border-gray-600">
                            Compare basic &amp; full
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
