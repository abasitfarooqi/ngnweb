<div>
{{-- Breadcrumbs --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="/bikes">Bikes</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $bike->make }} {{ $bike->model }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- Images --}}
        <div>
            @if(!$isNew && $bike->images && $bike->images->count() > 0)
                <img src="{{ $this->resolveImageUrl($bike->images->first()->url ?? ($saleInfo->image_one ?? '')) }}"
                     alt="{{ $bike->make }} {{ $bike->model }}"
                     class="w-full h-96 object-cover border border-gray-200 dark:border-gray-700 mb-3">
                @if($bike->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($bike->images->take(4) as $image)
                            <img src="{{ $this->resolveImageUrl($image->url ?? '') }}" alt="{{ $bike->make }} {{ $bike->model }}"
                                 class="w-full h-20 object-cover cursor-pointer hover:opacity-75 transition border border-gray-200 dark:border-gray-700">
                        @endforeach
                    </div>
                @endif
            @elseif(!$isNew && $saleInfo && $saleInfo->image_one)
                <img src="{{ $this->resolveImageUrl($saleInfo->image_one) }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-96 object-cover border border-gray-200 dark:border-gray-700">
            @else
                <div class="w-full h-96 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                    No images available
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div>
            <div class="mb-3">
                @if($isNew)
                    <flux:badge color="green">New</flux:badge>
                @else
                    <flux:badge color="blue">Used</flux:badge>
                @endif
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $bike->make }} {{ $bike->model }}
            </h1>

            <div class="mb-6">
                @if($isNew && $bike->price)
                    <div class="text-4xl font-bold text-brand-red mb-1">£{{ number_format($bike->price, 0) }}</div>
                @elseif(!$isNew && $saleInfo)
                    <div class="text-4xl font-bold text-brand-red mb-1">£{{ number_format($saleInfo->price ?? 0, 0) }}</div>
                @else
                    <div class="text-2xl font-bold text-gray-500 mb-1">Call for Price</div>
                @endif
                <p class="text-xs text-gray-500">Finance available – ask us for monthly options</p>
            </div>

            {{-- Key specs --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-5 mb-6 grid grid-cols-2 gap-4">
                @if($bike->year)   <div><p class="text-xs text-gray-500 mb-0.5">Year</p><p class="font-semibold text-gray-900 dark:text-white">{{ $bike->year }}</p></div> @endif
                @if($bike->engine ?? $bike->engine_capacity) <div><p class="text-xs text-gray-500 mb-0.5">Engine</p><p class="font-semibold text-gray-900 dark:text-white">{{ $bike->engine ?? $bike->engine_capacity }}</p></div> @endif
                @if($bike->color)  <div><p class="text-xs text-gray-500 mb-0.5">Colour</p><p class="font-semibold text-gray-900 dark:text-white">{{ $bike->color }}</p></div> @endif
                @if($bike->fuel_type) <div><p class="text-xs text-gray-500 mb-0.5">Fuel</p><p class="font-semibold text-gray-900 dark:text-white">{{ $bike->fuel_type }}</p></div> @endif
            </div>

            {{-- Actions --}}
            <div class="space-y-3 mb-6">
                @php
                    $financePrice = $isNew
                        ? (float) ($bike->sale_new_price ?? $bike->price ?? 0)
                        : (float) ($saleInfo->price ?? 0);
                @endphp
                <flux:button href="/finance?source={{ $isNew ? 'new-bike' : 'used-bike' }}&bike_id={{ $bike->id }}&bike_type={{ $isNew ? 'new' : 'used' }}&price={{ $financePrice }}" variant="outline" size="base" class="w-full">
                    Check Finance Options
                </flux:button>
                <flux:button href="tel:{{ config('site.phone') }}" variant="ghost" class="w-full">
                    Call Us: {{ config('site.phone') }}
                </flux:button>
            </div>

            @include('livewire.site.partials.sales.enquiry-form', ['submitAction' => 'submitEnquiry'])

            @if(!$isNew && $bike->branch)
                <flux:callout variant="info" icon="map-pin">
                    <flux:callout.heading>Available at {{ $bike->branch->name }}</flux:callout.heading>
                    <flux:callout.text>{{ $bike->branch->address ?? '' }}</flux:callout.text>
                </flux:callout>
            @endif
        </div>
    </div>

    {{-- Full specs --}}
    <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-10">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Full Specifications</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach([
                ['Make', $bike->make], ['Model', $bike->model], ['Reg', $bike->reg_no],
                ['Year', $bike->year], ['Engine', $bike->engine ?? $bike->engine_capacity],
                ['Colour', $bike->color], ['Fuel', $bike->fuel_type],
            ] as [$label, $value])
                @if($value)
                    <div class="bg-gray-50 dark:bg-gray-800 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $value }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Strip --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Ready to ride? Get in touch today.</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">
            Enquire Now
        </flux:button>
    </div>
</div>
</div>
