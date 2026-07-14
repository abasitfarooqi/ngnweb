@props([
    'motorbikes',
    'hasMore' => false,
    'infiniteScroll' => false,
])

@if($motorbikes->isEmpty())
    <div class="text-center py-16 md:py-24 border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-6">
        <flux:icon name="magnifying-glass" class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" />
        <p class="text-gray-900 dark:text-white font-semibold text-lg mb-2">No motorcycles match your filters</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">Try clearing the search or switching stock to “For sale”.</p>
        <button type="button" wire:click="resetFilters" class="bike-filter-btn bike-filter-btn-muted">View all for sale</button>
    </div>
@else
    <ul class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-8 list-none p-0 m-0 w-full" role="list">
        @foreach($motorbikes as $bike)
            @php
                $image = \App\Support\NgnMotorcycleImage::urlForUsedSale($bike->image_one ?? null);
                $isSold = (int) $bike->is_sold === 1;
                $regHint = $bike->reg_no ? '••••'.substr((string) $bike->reg_no, -3) : '';
            @endphp
            <li class="min-w-0 w-full" x-data="{ open: false }">
                <article class="flex h-full w-full flex-col overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:border-brand-red/50 transition-colors">
                    <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="relative block w-full overflow-hidden aspect-[5/3] min-h-[11rem] bg-gray-100 dark:bg-gray-800">
                        <img
                            src="{{ $image }}"
                            alt="{{ $bike->make }} {{ $bike->model }}"
                            width="500"
                            height="300"
                            decoding="async"
                            class="absolute inset-0 h-full w-full object-cover object-center"
                            loading="lazy"
                        >
                        <span class="absolute left-3 top-3 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white {{ $isSold ? 'bg-gray-800' : 'bg-emerald-700' }}">
                            {{ $isSold ? 'Sold' : 'For sale' }}
                        </span>
                    </a>
                    <div class="flex flex-1 flex-col p-4 md:p-5">
                        <h2 class="font-bold text-gray-900 dark:text-white text-base md:text-lg leading-snug">
                            <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="hover:text-brand-red transition-colors">
                                {{ $bike->make }} {{ $bike->model }}
                            </a>
                        </h2>
                        <p class="mt-1 text-xs md:text-sm text-gray-500 dark:text-gray-400">
                            @if($regHint)<span class="font-mono tabular-nums">{{ $regHint }}</span> · @endif
                            {{ $bike->year ?? '—' }}
                            @if($bike->engine)<span> · {{ $bike->engine }}</span>@endif
                        </p>
                        <p class="mt-3 text-2xl font-bold text-brand-red tabular-nums">
                            £{{ number_format((float) $bike->price, 2) }}
                        </p>
                        <button type="button" @click="open = !open" class="mt-2 text-xs font-semibold tracking-wide uppercase text-gray-600 dark:text-gray-300 text-left">
                            <span x-show="!open">Show details</span>
                            <span x-show="open" x-cloak>Hide details</span>
                        </button>
                        <dl x-show="open" x-cloak class="mt-2 grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] text-gray-600 dark:text-gray-300">
                            <div><dt class="font-semibold">Reg</dt><dd class="font-mono">{{ $regHint ?: '—' }}</dd></div>
                            <div><dt class="font-semibold">Year</dt><dd>{{ $bike->year ?? '—' }}</dd></div>
                            <div><dt class="font-semibold">Engine</dt><dd>{{ $bike->engine ?? '—' }}</dd></div>
                            <div><dt class="font-semibold">Colour</dt><dd>{{ $bike->color ?? '—' }}</dd></div>
                            <div class="col-span-2">
                                <dt class="font-semibold">Mileage</dt>
                                <dd>{{ isset($bike->sale_mileage) && $bike->sale_mileage !== null && $bike->sale_mileage !== '' ? number_format((float) $bike->sale_mileage) : '—' }}</dd>
                            </div>
                        </dl>
                        <div class="mt-auto pt-4 flex flex-col sm:flex-row gap-2">
                            <flux:button href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" variant="outline" size="sm" class="flex-1 justify-center">Details</flux:button>
                            <flux:button href="/finance?source=used-bike&bike_id={{ $bike->id }}&bike_type=used&price={{ (float) $bike->price }}" variant="filled" size="sm" class="flex-1 justify-center bg-brand-red text-white hover:bg-brand-red-dark">Payment plan</flux:button>
                        </div>
                    </div>
                </article>
            </li>
        @endforeach
    </ul>

    @if($infiniteScroll && $hasMore)
        <div
            x-data
            x-intersect:enter.margin.300px="$wire.loadMore()"
            class="py-10 text-center"
            wire:loading.class="opacity-60"
            wire:target="loadMore"
        >
            <p wire:loading wire:target="loadMore" class="text-sm text-gray-500 dark:text-gray-400">Loading more motorcycles…</p>
            <p wire:loading.remove wire:target="loadMore" class="text-xs text-gray-400 dark:text-gray-500">Scroll for more</p>
        </div>
    @elseif(! $infiniteScroll && method_exists($motorbikes, 'hasPages') && $motorbikes->hasPages())
        <div class="mt-10">
            {{ $motorbikes->links() }}
        </div>
    @endif
@endif
