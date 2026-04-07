<div>
<div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/25"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:text-left">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Motorcycle Repair Services</h1>
        <nav class="text-sm text-gray-400" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2 list-none p-0 m-0 justify-center md:justify-start">
                <li><a href="{{ route('site.home') }}" class="hover:text-white font-semibold underline-offset-2">Home Page</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('site.repairs') }}" class="hover:text-white font-semibold underline-offset-2">Repairs</a></li>
                <li aria-hidden="true">/</li>
                <li><span class="text-gray-300 font-semibold">Repair Services</span></li>
            </ol>
        </nav>
        <div class="mt-6 flex flex-wrap gap-2 justify-center md:justify-start">
            <flux:button href="{{ route('site.repairs.comparison') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">Compare servicing</flux:button>
            <flux:button href="{{ route('all-services') }}" variant="outline" size="sm" class="border-white/40 text-white hover:bg-white/10">All services</flux:button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <flux:callout variant="info" icon="information-circle" class="mb-10">
        <flux:callout.text>Your motorcycle deserves care and precision, whether it is for routine maintenance or addressing unexpected faults.</flux:callout.text>
    </flux:callout>

    @php
        $services = [
            ['icon' => 'magnifying-glass', 'title' => 'Fault Diagnosis', 'text' => 'Expert diagnosis to identify and resolve issues quickly and efficiently.'],
            ['icon' => 'cog-6-tooth', 'title' => 'Chain & Sprocket Replacement', 'text' => 'High-quality replacements to ensure smooth and reliable performance.'],
            ['icon' => 'document-text', 'title' => 'Brake Pad & Disc Replacement', 'text' => 'Ensure your safety with top-notch brake pad and disc replacements.'],
            ['icon' => 'exclamation-triangle', 'title' => 'Brake System Diagnosis & Repairs', 'text' => 'Comprehensive brake system checks and repairs for optimal safety.'],
            ['icon' => 'squares-2x2', 'title' => 'Tyre Replacement & Puncture Repairs', 'text' => 'Keep your ride smooth and safe with our tyre services.'],
            ['icon' => 'arrows-up-down', 'title' => 'Fork Servicing', 'text' => "Maintain your motorcycle's handling and performance with our fork services."],
            ['icon' => 'adjustments-horizontal', 'title' => 'Steering Bearing Replacement', 'text' => 'Ensure smooth and precise steering with our bearing replacement services.'],
            ['icon' => 'puzzle-piece', 'title' => 'Accessory Fitting', 'text' => "Professional fitting of accessories to enhance your motorcycle's functionality and style."],
            ['icon' => 'bolt', 'title' => 'Performance Parts Supply & Fitting', 'text' => 'Upgrade your motorcycle with high-performance parts and expert fitting.'],
            ['icon' => 'truck', 'title' => 'Insurance & Accident Damage Repairs', 'text' => 'Comprehensive repair services to get your motorcycle back on the road after an accident.'],
            ['icon' => 'document-text', 'title' => 'Charging System Testing & Repairs', 'text' => "Ensure your motorcycle's charging system is functioning optimally with our testing and repair services."],
            ['icon' => 'arrow-path', 'title' => 'Drive Belt & Rollers Replacement', 'text' => "Maintain your motorcycle's performance with our drive belt and rollers replacement services."],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $svc)
            <flux:card class="p-6 h-full border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-start gap-3 mb-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center bg-brand-red/10 text-brand-red border border-brand-red/20">
                        <flux:icon name="{{ $svc['icon'] }}" class="h-6 w-6" />
                    </span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug pt-1">{{ $svc['title'] }}</h3>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $svc['text'] }}</p>
            </flux:card>
        @endforeach
    </div>

    <flux:card class="mt-12 p-6 md:p-8 border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800/80">
        <h3 class="text-xl font-bold text-brand-red text-center mb-6">Additional Services</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <ul class="list-none p-0 m-0 font-semibold text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Fork drainage</li>
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Engine component repairs</li>
            </ul>
            <ul class="list-none p-0 m-0 font-semibold text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Electrical repairs</li>
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Drive shaft oil maintenance (e.g. Moto Guzzi)</li>
            </ul>
            <ul class="list-none p-0 m-0 font-semibold text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Oil changes by expert technicians</li>
                <li class="flex gap-2 items-start"><flux:icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" /> Comprehensive engine work</li>
            </ul>
        </div>
    </flux:card>

    <div class="mt-10 flex flex-col sm:flex-row flex-wrap gap-3 justify-center">
        <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Repairs']) }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark justify-center">
            Book a repair enquiry
        </flux:button>
        <flux:button href="{{ route('site.repairs') }}" variant="outline" class="justify-center border-slate-300 dark:border-gray-600">Repairs hub</flux:button>
    </div>

    <div class="mt-12 bg-gray-900 text-white border border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
            <h3 class="text-xl md:text-2xl font-bold text-brand-red mb-3">Visit NGN Motorbike Rentals &amp; Repairs</h3>
            <p class="text-gray-300 text-sm md:text-base mb-6 max-w-3xl mx-auto">Visit any of our three branches in Tooting, Sutton and Catford for expert motorcycle care. Let us help you keep your ride in top condition.</p>
            <div class="bg-black/50 p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left md:text-center">
                    <div>
                        <h4 class="font-semibold text-white mb-2">Catford Branch</h4>
                        <p class="text-sm text-gray-300">📞 <a href="tel:02083141498" class="text-white hover:text-brand-red">0208 314 1498</a></p>
                        <p class="text-xs text-gray-400 mt-2">
                            <a href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">9-13 Unit 1179 Catford Hill London SE6 4NU</a>
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-2">Tooting Branch</h4>
                        <p class="text-sm text-gray-300">📞 <a href="tel:02034095478" class="text-white hover:text-brand-red">0203 409 5478</a></p>
                        <p class="text-xs text-gray-400 mt-2">
                            <a href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">4A Penwortham Road, London SW16 6RE</a>
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-2">Sutton Branch</h4>
                        <p class="text-sm text-gray-300">📞 <a href="tel:02084129275" class="text-white hover:text-brand-red">0208 412 9275</a></p>
                        <p class="text-xs text-gray-400 mt-2">
                            <a href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">329 High St, Sutton SM1 1LW</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
