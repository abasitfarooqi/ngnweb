<div>
<div class="site-page-hero py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycles For Sale</h1>
        <p class="text-gray-300 text-lg mb-6">Quality used bikes &amp; new arrivals · Payment plans available</p>
        <flux:button href="/finance" variant="outline" class="border-white text-white hover:bg-white hover:text-gray-900">
            Check Payment Plan Options
        </flux:button>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 py-5  top-0 z-30 border-b border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex flex-wrap gap-2">
            @foreach(['all' => 'All', 'new' => 'New', 'used' => 'Used'] as $val => $label)
                <button
                    type="button"
                    wire:click="setFilter('{{ $val }}')"
                    class="h-10 px-4 text-sm font-semibold border {{ $filterType === $val ? 'bg-brand-red text-white border-brand-red' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:border-brand-red' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if(in_array($filterType, ['all', 'used'], true))
            @include('livewire.site.bikes.partials.used-filters', ['total' => $usedBikes?->total() ?? 0])
        @elseif($filterType === 'new')
            <div class="bike-list-filters max-w-xl">
                <label for="new-bike-search" class="bike-filter-label">Search new stock</label>
                <input id="new-bike-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Make or model" class="bike-filter-control">
            </div>
        @endif
    </div>
</div>

@if($usedBikes && in_array($filterType, ['all', 'used'], true))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 {{ $newBikes->count() > 0 && $filterType === 'all' ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Used Motorcycles</h2>
    @include('livewire.site.bikes.partials.used-bike-grid', ['motorbikes' => $usedBikes])
</div>
@endif

@if($newBikes->count() > 0 && in_array($filterType, ['all', 'new'], true))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">New Motorcycles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($newBikes as $bike)
            @php
                $fullPath = \App\Support\NgnMotorcycleImage::urlForNewStock($bike->file_path ?: ($bike->image ?? null));
            @endphp
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
                <a href="{{ route('new-motorcycle.detail', ['id' => $bike->id]) }}" class="block">
                    <img loading="lazy" src="{{ $fullPath }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-48 object-cover">
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $bike->make }} {{ $bike->model }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $bike->type }} @if($bike->engine) · {{ $bike->engine }}CC @endif
                    </p>
                    @if($bike->sale_new_price ?? $bike->price)
                        <p class="text-brand-red font-bold mt-1">£{{ number_format((float) ($bike->sale_new_price ?? $bike->price), 0) }}</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Call for price</p>
                    @endif
                    <div class="mt-3 flex flex-col gap-2">
                        <flux:button href="{{ route('new-motorcycle.detail', ['id' => $bike->id]) }}" variant="outline" size="sm" class="w-full">More information</flux:button>
                        <flux:button href="/finance?source=new-bike&bike_id={{ $bike->id }}&bike_type=new&price={{ (float) ($bike->sale_new_price ?? $bike->price ?? 0) }}" variant="outline" size="sm" class="w-full">Payment plan options</flux:button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endif

@if((! $usedBikes || $usedBikes->isEmpty()) && $newBikes->count() === 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No motorcycles match your filters. Try adjusting your search.</p>
    <button type="button" wire:click="resetFilters" class="bike-filter-btn">Clear filters</button>
</div>
@endif

<div class="site-page-hero py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold mb-3">Payment Plan Available</h2>
        <p class="text-gray-300 mb-6">Spread the cost with our flexible payment plan options. Check your eligibility today.</p>
        <flux:button href="/finance" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Check Payment Plan Options
        </flux:button>
    </div>
</div>
</div>
