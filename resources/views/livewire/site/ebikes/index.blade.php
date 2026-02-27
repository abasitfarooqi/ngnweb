<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Electric Bikes & E-Scooters</h1>
        <p class="text-gray-300 text-lg mb-6">The future of urban transport – zero emissions, all the fun</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Enquire Now
        </flux:button>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if($ebikes && $ebikes->count() > 0)
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Available E-Bikes</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($ebikes as $bike)
                <flux:card class="overflow-hidden p-0">
                    <div class="bg-gray-100 dark:bg-gray-800 h-48 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">{{ $bike->make }} {{ $bike->model }}</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->make }} {{ $bike->model }}</h3>
                        <p class="text-xs text-gray-500 mb-3">{{ $bike->engine ?? 'Electric' }} · {{ $bike->year ?? 'Current' }}</p>
                        @if($bike->price)
                            <p class="text-brand-red font-bold text-lg mb-3">£{{ number_format($bike->price, 0) }}</p>
                        @endif
                        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" size="sm" class="w-full bg-brand-red text-white">
                            Enquire
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <flux:callout variant="info" icon="information-circle" class="mb-10">
            <flux:callout.text>Contact us for our latest e-bike stock. New models arriving regularly.</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Benefits --}}
    <div class="mt-14 bg-gray-50 dark:bg-gray-900 p-10">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Why Go Electric?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            @foreach([
                ['icon'=>'bolt', 'title'=>'Zero Emissions', 'text'=>'No exhaust fumes. Better for London air.'],
                ['icon'=>'banknotes', 'title'=>'Lower Running Costs', 'text'=>'Charging costs pence vs litres of fuel.'],
                ['icon'=>'clock', 'title'=>'No Congestion Charge', 'text'=>'Save money every time you enter the zone.'],
                ['icon'=>'wrench-screwdriver', 'title'=>'Less Maintenance', 'text'=>'Fewer moving parts means fewer repairs.'],
            ] as $item)
                <div>
                    <div class="w-12 h-12 bg-brand-red flex items-center justify-center mx-auto mb-3">
                        <flux:icon name="{{ $item['icon'] }}" class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-1">{{ $item['title'] }}</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
</div>
