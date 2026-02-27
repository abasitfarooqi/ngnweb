<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">{{ $make }} {{ $model }} Rental</h1>
        <p class="text-gray-300 text-lg mb-6">Perfect for London commuting & weekend rides</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Book This Model
        </flux:button>
    </div>
</div>

{{-- Available bikes --}}
@if($bikes && $bikes->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Available {{ $make }} {{ $model }} Motorcycles</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($bikes as $bike)
                <flux:card class="overflow-hidden p-0">
                    @if($bike->images && $bike->images->count() > 0)
                        <img src="{{ $bike->images->first()->url }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">{{ $bike->make }} {{ $bike->model }}</div>
                    @endif
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $bike->year ?? 'Current' }} Model</h3>
                        @if($bike->color) <flux:badge class="mb-3">{{ $bike->color }}</flux:badge> @endif
                        <p class="text-brand-red text-2xl font-bold mb-1">From £{{ number_format($bike->currentRentingPricing->weekly_price ?? 80, 0) }}/week</p>
                        @if($bike->branch) <p class="text-xs text-gray-500 mb-4">{{ $bike->branch->name }} Branch</p> @endif
                        <flux:button href="/rentals/{{ $bike->id }}" variant="filled" size="sm" class="w-full bg-brand-red text-white">View Details</flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </div>
@else
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <p class="text-gray-500 dark:text-gray-400 text-lg mb-6">No {{ $make }} {{ $model }} currently available for rental.</p>
        <flux:button href="/rentals" variant="filled" class="bg-brand-red text-white">View All Rental Bikes</flux:button>
    </div>
@endif

{{-- Model info --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">About the {{ $make }} {{ $model }}</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            The {{ $make }} {{ $model }} is one of the most popular scooters for London commuting. With its blend of practicality, economy and style, it's ideal for navigating the city.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm uppercase tracking-wide">Key Features</h3>
                <ul class="space-y-2">
                    @foreach(['Excellent fuel economy (100+ mpg)', 'Lightweight & easy to handle', 'Ample under-seat storage', 'Low running costs', 'CBT rider friendly'] as $f)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" />{{ $f }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm uppercase tracking-wide">Ideal For</h3>
                <ul class="space-y-2">
                    @foreach(['Daily commuting in London', 'New riders with CBT', 'Economical city transport', 'Short to medium journeys'] as $f)
                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <flux:icon name="check-circle" class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" />{{ $f }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
