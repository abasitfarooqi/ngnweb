<div>
<div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/25"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Basic Motorcycle Service</h1>
        <nav class="text-sm text-gray-400" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2 list-none p-0 m-0">
                <li><a href="{{ route('site.home') }}" class="hover:text-white font-semibold underline-offset-2">Home Page</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('site.repairs.comparison') }}" class="hover:text-white font-semibold underline-offset-2">Compare Services</a></li>
                <li aria-hidden="true">/</li>
                <li><span class="text-gray-300 font-semibold">Basic Motorcycle Service</span></li>
            </ol>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <flux:callout variant="info" icon="information-circle" class="mb-10">
        <flux:callout.text>Our Basic Service package covers essential maintenance to keep your motorcycle running safely and efficiently.</flux:callout.text>
    </flux:callout>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach([
            ['title' => 'Engine Maintenance', 'items' => ['Oil Change', 'Oil Filter Replacement']],
            ['title' => 'Brakes', 'items' => ['Brake Pad & Disc Inspection', 'Brake Fluid Level Check', 'Brake Operation Test']],
            ['title' => 'Tyres & Wheels', 'items' => ['Tyre Pressure Check & Adjustment', 'Tread Depth Inspection', 'Wheel Condition Assessment']],
            ['title' => 'Chain/Drive Belt', 'items' => ['Chain Cleaning & Lubrication', 'Tension Adjustment', 'Drive Belt Inspection']],
            ['title' => 'Electrical System', 'items' => ['Light & Indicator Check', 'Battery Voltage Test', 'Horn Function Test']],
            ['title' => 'General Inspection', 'items' => ['Fastener Check & Tightening', 'Leak & Damage Inspection', 'Optional Test Ride']],
        ] as $block)
            <flux:card class="p-6 h-full border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-bold text-brand-red mb-4">{{ $block['title'] }}</h3>
                <ul class="list-none p-0 m-0 text-sm text-gray-700 dark:text-gray-300 space-y-2.5">
                    @foreach($block['items'] as $line)
                        <li class="flex gap-2 items-start">
                            <flux:icon name="check-circle" class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                            <span class="font-semibold leading-snug">{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @endforeach
    </div>

    <div class="mt-10 flex flex-col sm:flex-row flex-wrap gap-3 justify-center">
        <flux:button href="{{ route('site.contact.service-booking', ['service' => 'Motorcycle Basic Service']) }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark justify-center">
            Book your basic service
        </flux:button>
        <flux:button href="{{ route('site.repairs.comparison') }}" variant="outline" class="justify-center border-slate-300 dark:border-gray-600">
            Compare with full service
        </flux:button>
        <flux:button href="{{ route('site.repairs') }}" variant="outline" class="justify-center border-slate-300 dark:border-gray-600">
            Repairs hub
        </flux:button>
        <flux:button href="{{ route('all-services') }}" variant="outline" class="justify-center border-slate-300 dark:border-gray-600">
            All services
        </flux:button>
    </div>

    <x-site.repairs.branches-cta-dark
        heading="Book Your Basic Service Today"
        intro="Keep your motorcycle in perfect running condition with our comprehensive basic service package"
    />
</div>
</div>
