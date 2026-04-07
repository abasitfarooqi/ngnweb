<div>
    <div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:text-left">
            <h1 class="text-3xl md:text-5xl font-bold mb-3">Our Services</h1>
            <p class="text-gray-300 text-sm md:text-base max-w-2xl">Repairs, MOT, servicing, delivery, sales, rental, accident support and finance — three London branches.</p>
            <nav class="mt-4 text-sm text-gray-400" aria-label="Breadcrumb">
                <ol class="flex flex-wrap gap-2 list-none p-0 m-0 justify-center md:justify-start font-semibold">
                    <li><a href="{{ route('site.home') }}" class="hover:text-white underline-offset-2">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li><span class="text-gray-300">Our Services</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            <div class="lg:col-span-8">
                <flux:accordion class="border border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-800 [&_button[data-flux-accordion-heading]]:text-gray-900 [&_button[data-flux-accordion-heading]]:dark:text-gray-100 [&_[data-flux-accordion-content]>div]:!text-gray-600 [&_[data-flux-accordion-content]>div]:dark:!text-gray-300">
                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Motorcycle Repairs" :expanded="$openPanel === 'Repairs'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/repairs.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3">NGN provides expert motorcycle repairs, maintenance, and MOT services in South London. Our skilled mechanics deliver top-quality service for Japanese and European bikes, with a reputation for reliability and competitive pricing.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('site.repairs.full') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Full service details</flux:button>
                            <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Call Catford</flux:button>
                            <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Repairs']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Enquire</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="MOT Services" :expanded="$openPanel === 'MOT'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/MOT-BOOKING.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3">Fully qualified testers and mechanics ensure your bike meets current inspection standards. Book online or by phone for peace of mind on the road.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('site.mot') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">MOT information</flux:button>
                            <flux:button href="{{ route('site.contact.service-booking', ['service' => 'MOT Booking Enquiry']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Book MOT enquiry</flux:button>
                            <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Call now</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Motorcycle Servicing / Maintenance" :expanded="$openPanel === 'BookService'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/full-service.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-2">Regular servicing keeps your motorcycle safe, reliable, and holding its value. We cover brakes, steering, suspension, tyres and more.</p>
                        <p class="leading-relaxed mb-3 text-sm">Compare packages on our <a href="{{ route('site.repairs.comparison') }}" class="text-brand-red font-semibold underline underline-offset-2">service comparison</a> page.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('site.repairs.basic') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Basic service</flux:button>
                            <flux:button href="{{ route('site.repairs.full') }}" variant="filled" size="sm" class="bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700">Full service</flux:button>
                            <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Basic Service']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Enquire — basic</flux:button>
                            <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Full Service']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Enquire — full</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Vehicle Delivery Service" :expanded="$openPanel === 'Delivery'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/wide-motorbike-recovery.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-2"><strong class="text-gray-900 dark:text-white">NGN specialises in secure, efficient motorcycle transport</strong> for private and commercial clients.</p>
                        <p class="leading-relaxed mb-2"><strong class="text-gray-900 dark:text-white">Enjoy free collection</strong> when you choose our repair service.</p>
                        <p class="leading-relaxed mb-3"><strong class="text-gray-900 dark:text-white">Nationwide transport from £249.99</strong> anywhere in England.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('motorcycle.delivery') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Delivery &amp; recovery</flux:button>
                            <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Call now</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Motorcycle Sales" :expanded="$openPanel === 'Sales'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/new-and-used-motorcycles-for-sale-in-london.png') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3">New and used motorcycles from leading brands — flexible finance on new stock, and practical used options ideal for London riders and delivery work.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('motorcycles.new') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">New motorcycles</flux:button>
                            <flux:button href="{{ route('motorcycles.used') }}" variant="filled" size="sm" class="bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700">Used motorcycles</flux:button>
                            <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Call now</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Motorcycle Rental" :expanded="$openPanel === 'Rental'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/motorcycle-rental-hire-london.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3">Rent motorcycles across London, Tooting, Sutton and Catford — prices from £70/week.</p>
                        <div class="flex flex-wrap gap-2">
                            <flux:button href="{{ route('site.rentals') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Rental fleet</flux:button>
                            <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Rental Enquiry']) }}" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Rental enquiry</flux:button>
                            <flux:button href="tel:02083141498" variant="outline" size="sm" class="border-gray-300 dark:border-gray-600">Call now</flux:button>
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item class="!border-b border-gray-200 px-3 dark:!border-gray-600" heading="Accident Management Services" :expanded="$openPanel === 'Accident'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/accident.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3"><strong class="text-gray-900 dark:text-white">We are experts in road traffic accidents.</strong></p>
                        <flux:button href="{{ route('accident-management') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Accident management</flux:button>
                    </flux:accordion.item>

                    <flux:accordion.item class="px-3 !pb-5" heading="Finance Services" :expanded="$openPanel === 'Finance'" :transition="true">
                        <img loading="lazy" src="{{ asset('assets/images/services/finance.jpg') }}" alt="" class="w-full max-h-48 object-cover mb-4 md:hidden border border-gray-200 dark:border-gray-600">
                        <p class="leading-relaxed mb-3">Explore finance options to make your motorcycle purchase easier.</p>
                        <flux:button href="{{ route('site.finance') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Finance overview</flux:button>
                    </flux:accordion.item>
                </flux:accordion>
            </div>

            <div class="hidden lg:block lg:col-span-4">
                <div class="sticky top-28 space-y-6">
                    <img loading="lazy" src="{{ asset('assets/images/services/repairs.jpg') }}" alt="NGN workshop and motorcycle services" class="w-full object-cover border border-gray-200 dark:border-gray-600 shadow-md">
                    <flux:card class="p-5 border border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900">
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-2">Quick links</h3>
                        <ul class="text-sm space-y-2 list-none p-0 m-0">
                            <li><a href="{{ route('site.repairs') }}" class="text-brand-red font-semibold underline underline-offset-2">Repairs hub</a></li>
                            <li><a href="{{ route('site.repairs.comparison') }}" class="text-brand-red font-semibold underline underline-offset-2">Compare services</a></li>
                            <li><a href="{{ route('site.repairs.repair-services') }}" class="text-brand-red font-semibold underline underline-offset-2">Repair menu</a></li>
                            <li><a href="{{ route('site.mot') }}" class="text-brand-red font-semibold underline underline-offset-2">MOT</a></li>
                        </ul>
                    </flux:card>
                </div>
            </div>
        </div>

        <x-site.repairs.branches-cta-dark heading="Speak to a branch" intro="Catford, Tooting and Sutton — call or visit for bookings and advice on any service above." />
    </div>
</div>
