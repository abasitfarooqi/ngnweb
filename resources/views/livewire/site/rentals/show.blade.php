<div>
{{-- Breadcrumbs --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="/rentals">Rentals</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $motorbike->make }} {{ $motorbike->model }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- Images --}}
        <div>
            @if($motorbike->images && $motorbike->images->count() > 0)
                <img src="{{ $motorbike->images->first()->url ?? asset('images/placeholder-bike.jpg') }}"
                     alt="{{ $motorbike->make }} {{ $motorbike->model }}"
                     class="w-full h-96 object-cover mb-3 border border-gray-200 dark:border-gray-700">
                @if($motorbike->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($motorbike->images->take(4) as $image)
                            <img src="{{ $image->url }}" alt="{{ $motorbike->make }} {{ $motorbike->model }}"
                                 class="w-full h-20 object-cover cursor-pointer hover:opacity-75 transition border border-gray-200 dark:border-gray-700">
                        @endforeach
                    </div>
                @endif
            @else
                <div class="w-full h-96 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                    No images available
                </div>
            @endif
        </div>

        {{-- Details + Booking --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                {{ $motorbike->make }} {{ $motorbike->model }}
            </h1>

            <div class="flex flex-wrap gap-2 mb-5">
                @foreach([$motorbike->year, $motorbike->engine, $motorbike->color, $motorbike->fuel_type] as $spec)
                    @if($spec) <flux:badge>{{ $spec }}</flux:badge> @endif
                @endforeach
            </div>

            {{-- Period selector --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-5 mb-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Select rental period:</p>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach(['daily'=>'Daily', 'weekly'=>'Weekly', 'monthly'=>'Monthly'] as $val => $label)
                        <button
                            wire:click="setPeriod('{{ $val }}')"
                            class="py-2.5 px-3 border-2 text-sm font-medium transition {{ $selectedPeriod === $val ? 'border-brand-red bg-brand-red text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-brand-red mb-1">£{{ number_format($currentPrice, 2) }}</p>
                    <p class="text-sm text-gray-500">per {{ $selectedPeriod === 'daily' ? 'day' : ($selectedPeriod === 'weekly' ? 'week' : 'month') }}</p>
                </div>
            </div>

            <flux:button
                x-data @click="$flux.modal('quick-book').show()"
                variant="filled"
                size="base"
                class="w-full bg-brand-red text-white hover:bg-brand-red-dark mb-4"
            >
                Book This Motorcycle
            </flux:button>

            @if($motorbike->branch)
                <flux:callout variant="info" icon="map-pin" class="mb-4">
                    <flux:callout.heading>Available at {{ $motorbike->branch->name }}</flux:callout.heading>
                    <flux:callout.text>{{ $motorbike->branch->address ?? '' }}</flux:callout.text>
                </flux:callout>
            @endif

            <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm uppercase tracking-wide">Rental Requirements</h3>
                <ul class="space-y-2">
                    @foreach([
                        'Valid UK driving licence (Cat A or CBT)',
                        'Minimum age: 21 years',
                        'Refundable deposit: £' . ($pricing->minimum_deposit ?? 200),
                        'Proof of address & ID required',
                        'Full insurance included',
                    ] as $req)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                            {{ $req }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Full specs --}}
    <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-10">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Full Specifications</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach([
                ['Make', $motorbike->make], ['Model', $motorbike->model], ['Year', $motorbike->year],
                ['Engine', $motorbike->engine], ['Colour', $motorbike->color], ['Fuel', $motorbike->fuel_type],
                ['Reg', $motorbike->reg_no], ['Type', $motorbike->type_approval],
            ] as [$label, $value])
                @if($value)
                    <div class="bg-gray-50 dark:bg-gray-800 p-4">
                        <p class="text-xs text-gray-500 mb-1">{{ $label }}</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $value }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- What's included --}}
    <div class="mt-10 bg-gray-50 dark:bg-gray-800 p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">What's Included</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach([
                'Comprehensive insurance', '24/7 breakdown assistance',
                'Flexible rental periods', 'Full service history',
                'No hidden fees', 'Road tax included',
            ] as $item)
                <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <flux:icon name="check-circle" class="h-5 w-5 text-green-500 flex-shrink-0" />
                    {{ $item }}
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Ready to book this bike?</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">
            Book Now
        </flux:button>
    </div>
</div>
</div>
