@switch($panel)
    @case('Repairs')
        <p>NGN provides expert motorcycle repairs, maintenance, and MOT services in South London. Our skilled mechanics deliver top-quality service for Japanese and European bikes, earning a reputation for reliability and competitive pricing. Trust NGN for the care and restoration of your motorcycle.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('site.repairs.full') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Find out more</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'Repairs']) }}#service-enquiry" variant="outline" size="sm">Enquire</flux:button>
        </div>
        @break
    @case('MOT')
        <p>Experience exceptional motorcycle MOT services at NGN&apos;s specialist workshop in South London. Our fully qualified testers and mechanics ensure your bike complies with all technical inspection requirements. Rely on us for quality service and book your motorcycle MOT today for peace of mind on the road.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('site.mot') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">MOT information</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'MOT']) }}#service-enquiry" variant="outline" size="sm">Enquire</flux:button>
        </div>
        @break
    @case('BookService')
        <p>Regular servicing and maintenance are crucial for keeping your motorcycle performing at its best, ensuring safety, and maintaining its value.</p>
        <p>Maintenance covers essential safety areas like brakes, steering, suspension, and tyres, helping to identify potential issues before they arise.</p>
        <p>Our experienced technicians at NGN Motorcycle Service Centre provide high-quality service. Book your Yamaha or Honda motorcycle service today.</p>
        <p class="text-sm">For a detailed comparison of our service options, visit our <a href="{{ route('site.repairs.comparison') }}" class="text-brand-red font-semibold underline underline-offset-2">Service comparison</a> page.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('site.repairs.basic') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Basic service</flux:button>
            <flux:button href="{{ route('site.repairs.full') }}" variant="filled" size="sm" class="bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700">Full service</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'BookService', 'booking' => 'Motorcycle Basic Service Enquiry']) }}#service-enquiry" variant="outline" size="sm">Enquire — basic</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'BookService', 'booking' => 'Motorcycle Full Service Enquiry']) }}#service-enquiry" variant="outline" size="sm">Enquire — full</flux:button>
        </div>
        @break
    @case('Delivery')
        <p><strong class="text-gray-900 dark:text-white">NGN specialises in secure, efficient motorcycle transport solutions</strong> that safeguard your ride, simplify logistics, and enhance service quality for both private and commercial clients.</p>
        <p><strong class="text-gray-900 dark:text-white">Enjoy free collection</strong> when you choose our repair service.</p>
        <p><strong class="text-gray-900 dark:text-white">Nationwide transport services from £249.99</strong> anywhere in England.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('motorcycle.delivery') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Delivery &amp; recovery</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
            <flux:button href="{{ route('motorcycle.delivery') }}" variant="outline" size="sm">Delivery page</flux:button>
        </div>
        @break
    @case('Sales')
        <p><strong class="text-gray-900 dark:text-white">Discover our motorcycle sales.</strong> Explore our <strong class="text-gray-900 dark:text-white">2025 range</strong> of motorbikes and mopeds from top brands like <strong class="text-gray-900 dark:text-white">Honda</strong> and <strong class="text-gray-900 dark:text-white">Yamaha</strong>, ideal for city commuting in London and perfect for new riders. Popular models include the <strong class="text-gray-900 dark:text-white">Honda PCX125</strong>, <strong class="text-gray-900 dark:text-white">Vision 110</strong>, <strong class="text-gray-900 dark:text-white">CB125F</strong>, <strong class="text-gray-900 dark:text-white">CBR125R</strong>, <strong class="text-gray-900 dark:text-white">CRF250L</strong>, and <strong class="text-gray-900 dark:text-white">MSX125 Grom</strong>. For Yamaha, we offer the <strong class="text-gray-900 dark:text-white">YZF-R125</strong>, <strong class="text-gray-900 dark:text-white">MT-125</strong>, <strong class="text-gray-900 dark:text-white">NMAX 125</strong>, <strong class="text-gray-900 dark:text-white">FZ-125</strong>, <strong class="text-gray-900 dark:text-white">WR125R</strong>, <strong class="text-gray-900 dark:text-white">Yamaha Aerox 50</strong>, and <strong class="text-gray-900 dark:text-white">Yamaha Neo&apos;s 50</strong>.</p>
        <p>Looking for a reliable bike with <strong class="text-gray-900 dark:text-white">flexible payment options</strong>? We offer brand-new motorbikes from the <strong class="text-gray-900 dark:text-white">2025 range</strong> with straightforward instalment plans tailored to your budget. Our used bikes are sold as-is, making them a practical choice for delivery riders across London.</p>
        <p><strong class="text-gray-900 dark:text-white">Straightforward payments</strong> designed to suit different budgets.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('motorcycles.new') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">New motorcycles</flux:button>
            <flux:button href="{{ route('motorcycles.used') }}" variant="filled" size="sm" class="bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700">Used motorcycles</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
        </div>
        @break
    @case('Rental')
        <p>Rent motorcycles in London, Tooting, Sutton, and Catford with prices starting from £70/week.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('site.rentals') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Find out more</flux:button>
            <flux:button href="tel:02083141498" variant="outline" size="sm">Call now</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'Rental']) }}#service-enquiry" variant="outline" size="sm">Enquire</flux:button>
        </div>
        @break
    @case('Accident')
        <p><strong class="text-gray-900 dark:text-white">We are experts in road traffic accidents.</strong></p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('accident-management') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Make a claim</flux:button>
            <flux:button href="{{ route('all-services', ['service' => 'Accident']) }}#service-enquiry" variant="outline" size="sm">Enquire</flux:button>
        </div>
        @break
    @case('Finance')
        <p><strong class="text-gray-900 dark:text-white">Explore our finance options</strong> to make your motorcycle purchase easier.</p>
        <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-600">
            <flux:button href="{{ route('site.finance') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">Finance overview</flux:button>
        </div>
        @break
@endswitch
