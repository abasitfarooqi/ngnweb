<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">About NGN Motors</h1>
        <p class="text-gray-300 text-lg">London's trusted motorcycle specialists since 2018</p>
    </div>
</div>

{{-- Story --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-5">Our Story</h2>
            <div class="space-y-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                <p>Incorporated in October 2018, <strong>Neguinho Motors Limited (NGN)</strong> was founded with a single vision: to provide London riders with a trusted, reliable and comprehensive motorcycle service centre.</p>
                <p>We started at our Catford branch and have since expanded to three London locations – Catford, Tooting and Sutton – serving thousands of riders across the city.</p>
                <p>Our mission is to keep your motorcycle roadworthy and performing at its best, offering everything from daily rentals and MOT testing to full workshop repairs and motorcycle sales.</p>
            </div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 h-72 flex items-center justify-center">
            <img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-24 w-auto opacity-50">
        </div>
    </div>
</div>

{{-- Services overview --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10 text-center">What We Do</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon'=>'clock',            'title'=>'Motorcycle Rentals',   'text'=>'Daily, weekly & monthly rentals. Honda & Yamaha 125cc. CBT friendly. From £80/week.', 'url'=>'/rentals'],
                ['icon'=>'check-badge',      'title'=>'MOT Testing',          'text'=>'DVSA-approved MOT testing for motorcycles, mopeds & scooters. From £29.65.', 'url'=>'/mot'],
                ['icon'=>'wrench-screwdriver','title'=>'Repairs & Servicing', 'text'=>'Full workshop repairs, basic & major services, accident damage, brake & tyre work.', 'url'=>'/repairs'],
                ['icon'=>'shopping-bag',     'title'=>'Sales',                'text'=>'New and used motorcycles from trusted brands. Finance options available.', 'url'=>'/bikes'],
                ['icon'=>'banknotes',        'title'=>'Finance',              'text'=>'Flexible finance plans to help you buy your dream bike. Competitive rates.', 'url'=>'/finance'],
                ['icon'=>'bolt',             'title'=>'Recovery',             'text'=>'24/7 motorcycle recovery, breakdown assistance and delivery across London.', 'url'=>'/recovery'],
            ] as $item)
                <a href="{{ $item['url'] }}" class="group block">
                    <flux:card class="p-6 h-full hover:border-brand-red transition">
                        <div class="w-10 h-10 bg-brand-red flex items-center justify-center mb-4">
                            <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 text-white" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-red transition">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $item['text'] }}</p>
                    </flux:card>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="py-16 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([['2018', 'Est.'], ['3', 'London Branches'], ['5,000+', 'Customers Served'], ['1,200+', 'MOTs Completed']] as [$num, $label])
                <div>
                    <p class="text-4xl font-bold text-brand-red mb-1">{{ $num }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold mb-3">Ready to experience the NGN difference?</h2>
        <div class="flex justify-center gap-4 flex-wrap">
            <flux:button href="/contact" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">Contact Us</flux:button>
            <flux:button href="/locations" variant="outline" class="border-white text-white hover:bg-white hover:text-brand-red">Find a Branch</flux:button>
        </div>
    </div>
</div>
</div>
