<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl font-bold mb-2">{{ strtoupper($make.' '.$model) }}</h1>
        <p class="text-gray-300 text-sm md:text-base uppercase tracking-wide">Category: URBAN MOBILITY</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <div>
            @php $heroImg = ! empty($pageMeta['hero_image']) ? asset($pageMeta['hero_image']) : asset('images/placeholder-bike.jpg'); @endphp
            <img src="{{ $heroImg }}" alt="{{ $make }} {{ $model }}" class="w-full h-auto object-cover border border-gray-200 dark:border-gray-700">
        </div>

        <div class="space-y-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ $pageMeta['tagline'] ?? '' }}</p>
                <p class="text-4xl md:text-5xl font-bold text-brand-red">£{{ number_format($currentPrice, 0) }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">per {{ $selectedPeriod === 'daily' ? 'day' : ($selectedPeriod === 'weekly' ? 'week' : 'month') }}</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Select rental period:</p>
                <div class="grid grid-cols-3 gap-2 mb-2">
                    @foreach(['daily'=>'Daily', 'weekly'=>'Weekly', 'monthly'=>'Monthly'] as $val => $label)
                        <button
                            type="button"
                            wire:click="setPeriod('{{ $val }}')"
                            class="py-2.5 px-2 border-2 text-xs sm:text-sm font-medium transition {{ $selectedPeriod === $val ? 'border-brand-red bg-brand-red text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Requirements for rental</h2>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300 list-disc pl-5">
                    <li>Driving licence</li>
                    <li>Proof of address</li>
                    <li>Proof of identification</li>
                    <li>Insurance certification</li>
                    <li>CBT certification</li>
                    <li>£300 deposit</li>
                    <li>6 weeks minimum rental period</li>
                </ul>
            </div>

            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p>You need to bring a lock and chain before collecting the motorcycle. If you do not have one you can always purchase from our shop along with lots of other motorcycle accessories.</p>
                <p>Insurance fire and theft is the minimum cover we accept. The motorcycle must be locked at all times.</p>
                <p>Any damage must be paid by you or a claim must be made under your insurance.</p>
                <p>You must give one week notice before returning the motorcycle or deposit will be lost.</p>
                <p>Deposit will be refunded provided there is no damage to the motorcycle and no accessories are missing.</p>
            </div>

            <a href="tel:02083141498" class="inline-block font-semibold text-brand-red hover:underline">Please call 0208 314 1498</a>

            @include('livewire.site.partials.sales.enquiry-form', [
                'submitAction' => 'submitEnquiry',
                'enquiryTypeLabel' => 'Motorcycle rental (model)',
            ])
        </div>
    </div>
</div>

{{-- Optional fleet matches (same model line) --}}
@if($bikes && $bikes->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Available {{ $make }} {{ $model }} motorcycles</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($bikes as $bike)
                <flux:card class="overflow-hidden p-0 border border-gray-200 dark:border-gray-700">
                    @if($bike->images && $bike->images->count() > 0)
                        <img src="{{ $bike->images->first()->url }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-sm">{{ $bike->make }} {{ $bike->model }}</div>
                    @endif
                    <div class="p-5">
                        <p class="text-brand-red font-bold mb-3">From £{{ number_format($bike->currentRentingPricing->weekly_price ?? $pageMeta['weekly_base'], 0) }}/week</p>
                        <div class="grid grid-cols-2 gap-2">
                            <flux:button wire:click="selectBike({{ $bike->id }})" variant="outline" size="sm" class="w-full">Use live price</flux:button>
                            <flux:button href="/rentals/{{ $bike->id }}" variant="filled" size="sm" class="w-full bg-brand-red text-white">View details</flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-4">Choosing a bike above switches the period calculator to that vehicle&apos;s weekly rate. Your enquiry can always be for this model line without selecting a specific bike.</p>
    </div>
@endif

</div>
