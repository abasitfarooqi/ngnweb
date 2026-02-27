<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycles For Sale</h1>
        <p class="text-gray-300 text-lg mb-6">Quality used bikes & new arrivals · Finance available</p>
        <flux:button href="/finance" variant="outline" class="border-white text-white hover:bg-white hover:text-gray-900">
            Check Finance Options
        </flux:button>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-900 py-5 sticky top-14 z-30 border-b border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="flex gap-1">
                @foreach(['all'=>'All', 'new'=>'New', 'used'=>'Used'] as $val => $label)
                    <flux:button
                        wire:click="setFilter('{{ $val }}')"
                        variant="{{ $filterType === $val ? 'filled' : 'outline' }}"
                        size="sm"
                        class="{{ $filterType === $val ? 'bg-brand-red text-white border-brand-red' : '' }}"
                    >
                        {{ $label }}
                    </flux:button>
                @endforeach
            </div>
            <div class="flex-1 min-w-48">
                <flux:input wire:model.live.debounce.500ms="searchMake" placeholder="Search by make (e.g. Honda, Yamaha)" size="sm" />
            </div>
            <div class="flex gap-2 items-center">
                <flux:input wire:model.live.debounce.500ms="minPrice" type="number" placeholder="Min £" size="sm" class="w-24" />
                <span class="text-gray-400 text-sm">–</span>
                <flux:input wire:model.live.debounce.500ms="maxPrice" type="number" placeholder="Max £" size="sm" class="w-24" />
            </div>
        </div>
    </div>
</div>

{{-- New Bikes --}}
@if($newBikes->count() > 0 && in_array($filterType, ['all', 'new']))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">New Motorcycles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($newBikes as $bike)
            <flux:card class="overflow-hidden p-0">
                <div class="relative bg-gray-100 dark:bg-gray-800 h-44">
                    <flux:badge color="green" class="absolute top-2 right-2 text-xs font-semibold">New</flux:badge>
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm px-4 text-center">{{ $bike->make }} {{ $bike->model }}</div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->make }} {{ $bike->model }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $bike->engine_capacity ?? 'N/A' }} · {{ $bike->year ?? 'Current' }}</p>
                    @if($bike->price)
                        <p class="text-brand-red font-bold text-lg mb-3">£{{ number_format($bike->price, 0) }}</p>
                    @else
                        <p class="text-gray-500 text-sm mb-3">Call for price</p>
                    @endif
                    <flux:button href="/bikes/new/{{ $bike->id }}" variant="outline" size="sm" class="w-full">View Details</flux:button>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>
@endif

{{-- Used Bikes --}}
@if($usedBikes->count() > 0 && in_array($filterType, ['all', 'used']))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 {{ $newBikes->count() > 0 ? 'border-t border-gray-200 dark:border-gray-700' : '' }}">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Used Motorcycles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($usedBikes as $bike)
            <flux:card class="overflow-hidden p-0">
                <div class="relative bg-gray-100 dark:bg-gray-800 h-44">
                    <flux:badge color="blue" class="absolute top-2 right-2 text-xs font-semibold">Used</flux:badge>
                    @if($bike->image_one)
                        <img src="{{ $bike->image_one }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm px-4 text-center">{{ $bike->make }} {{ $bike->model }}</div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->make }} {{ $bike->model }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $bike->engine ?? 'N/A' }} · {{ $bike->year ?? 'N/A' }}</p>
                    <p class="text-brand-red font-bold text-lg mb-3">£{{ number_format($bike->price ?? 0, 0) }}</p>
                    <flux:button href="/bikes/used/{{ $bike->id }}" variant="outline" size="sm" class="w-full">View Details</flux:button>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>
@endif

@if($newBikes->count() === 0 && $usedBikes->count() === 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No motorcycles match your filters. Try adjusting your search.</p>
    <flux:button wire:click="setFilter('all')" variant="filled" class="bg-brand-red text-white">Clear Filters</flux:button>
</div>
@endif

{{-- Finance CTA --}}
<div class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold mb-3">Finance Available</h2>
        <p class="text-gray-300 mb-6">Spread the cost with our flexible finance options. Check your eligibility today.</p>
        <flux:button href="/finance" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Check Finance Options
        </flux:button>
    </div>
</div>
</div>
