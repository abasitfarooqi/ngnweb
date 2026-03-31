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

{{-- Used Bikes --}}
@if($usedBikes->count() > 0 && in_array($filterType, ['all', 'used']))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 {{ $newBikes->count() > 0 ? 'border-t border-gray-200 dark:border-gray-700' : '' }}">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Used Motorcycles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($usedBikes as $bike)
            @php
                $img = $bike->image_one
                    ? 'https://neguinhomotors.co.uk/storage/motorbikes/'.$bike->image_one
                    : 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                $maskedReg = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : '';
            @endphp
            <article
                x-data="{ open: false }"
                class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900 flex flex-col"
            >
                <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="block">
                    <img
                        src="{{ $img }}"
                        alt="{{ $bike->make }} {{ $bike->model }}"
                        class="w-full h-48 object-cover"
                        loading="lazy"
                    >
                </a>
                <div class="p-4 flex flex-col gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="hover:text-brand-red transition-colors">
                                {{ $bike->make }} {{ $bike->model }}
                            </a>
                        </h3>
                        @if($maskedReg)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Reg No. <span class="font-mono tabular-nums">{{ $maskedReg }}</span>
                            </p>
                        @endif
                    </div>
                    <p class="text-brand-red font-bold text-lg">
                        £{{ number_format((float) $bike->price, 2) }}
                    </p>
                    <div class="mt-1">
                        <button
                            type="button"
                            @click="open = !open"
                            class="text-xs font-semibold tracking-wide uppercase text-gray-600 dark:text-gray-300 flex items-center gap-1"
                        >
                            <span x-show="!open">Show details</span>
                            <span x-show="open" x-cloak>Hide details</span>
                        </button>
                    </div>
                    <dl
                        x-show="open"
                        x-cloak
                        class="mt-2 ml-1 grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] text-gray-600 dark:text-gray-300"
                    >
                        <div>
                            <dt class="font-semibold">Reg No.</dt>
                            <dd class="font-mono tabular-nums">{{ $maskedReg ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Year</dt>
                            <dd>{{ $bike->year ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Engine</dt>
                            <dd>{{ $bike->engine ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Colour</dt>
                            <dd>{{ $bike->color ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Mileage</dt>
                            <dd>
                                @if(isset($bike->sale_mileage) && $bike->sale_mileage !== null)
                                    {{ number_format((float) $bike->sale_mileage) }}
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-3 flex flex-col gap-2">
                        <flux:button
                            href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}"
                            variant="outline"
                            size="sm"
                            class="w-full justify-center"
                        >
                            View full details
                        </flux:button>
                        <flux:button
                            href="/finance?source=used-bike&bike_id={{ $bike->id }}&bike_type=used&price={{ (float) $bike->price }}"
                            variant="outline"
                            size="sm"
                            class="w-full justify-center"
                        >
                            Finance options
                        </flux:button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endif


{{-- New Bikes --}}
@if($newBikes->count() > 0 && in_array($filterType, ['all', 'new']))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Discover More Bikes   </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($newBikes as $bike)
            @php
                $imagePath = $bike->file_path ?? null;
                $fullPath = '';

                if ($imagePath) {
                    if (is_string($imagePath)) {
                        if (str_starts_with($imagePath, '/storage/uploads/') || str_starts_with($imagePath, '/storage/motorbikes/')) {
                            $fullPath = 'https://neguinhomotors.co.uk'.$imagePath;
                        } else {
                            $fullPath = 'https://neguinhomotors.co.uk/storage/motorbikes/'.$imagePath;
                        }
                    }
                }

                if (empty($fullPath)) {
                    $fullPath = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                }
            @endphp
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
                <a href="{{ route('new-motorcycle.detail', ['id' => $bike->id]) }}" class="block">
                    <img
                        loading="lazy"
                        src="{{ $fullPath }}"
                        alt="{{ $bike->make }} {{ $bike->model }}"
                        class="w-full h-48 object-cover"
                    >
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $bike->make }} {{ $bike->model }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $bike->type }} @if($bike->engine) · {{ $bike->engine }}CC @endif
                    </p>
                    @if($bike->sale_new_price ?? $bike->price)
                        <p class="text-brand-red font-bold mt-1">
                            GBP {{ number_format((float) ($bike->sale_new_price ?? $bike->price), 2) }}
                        </p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Call for price</p>
                    @endif
                    <div class="mt-3 flex flex-col gap-2">
                        <flux:button href="{{ route('new-motorcycle.detail', ['id' => $bike->id]) }}" variant="outline" size="sm" class="w-full">
                            More information
                        </flux:button>
                        <flux:button href="/finance?source=new-bike&bike_id={{ $bike->id }}&bike_type=new&price={{ (float) ($bike->sale_new_price ?? $bike->price ?? 0) }}" variant="outline" size="sm" class="w-full">
                            Finance options
                        </flux:button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endif
<!-- //here! -->



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
