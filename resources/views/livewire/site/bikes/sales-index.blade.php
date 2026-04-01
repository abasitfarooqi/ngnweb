<div>
    <section class="relative bg-gray-900 text-white overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900 to-brand-red/25 opacity-90" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <flux:badge color="green" class="mb-4 uppercase tracking-widest text-[10px]">New stock</flux:badge>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight">Motorcycles for sale</h1>
            <p class="mt-3 max-w-2xl text-sm md:text-base text-gray-300 leading-relaxed">
                Same listings as <a href="{{ route('site.bikes') }}" class="text-white underline hover:text-brand-red">/bikes</a> — full photos, specifications and finance links.
            </p>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            <form method="GET" action="{{ route('sale-motorcycles') }}" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-4 items-end">
                    <div class="sm:col-span-2 lg:col-span-5 w-full">
                        <label for="sales-search" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                        <input
                            id="sales-search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Make or model"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-transparent"
                        >
                    </div>
                    <div class="w-full lg:col-span-4">
                        <label for="sales-sort" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Sort</label>
                        <select
                            id="sales-sort"
                            name="sort"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                        >
                            <option value="default" @selected($sort === 'default')>Newest listed</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                        </select>
                    </div>
                    <div class="w-full lg:col-span-3 flex gap-2">
                        <flux:button type="submit" variant="filled" class="w-full justify-center bg-brand-red text-white hover:bg-brand-red-dark">
                            Apply
                        </flux:button>
                        <flux:button href="{{ route('sale-motorcycles') }}" variant="outline" class="w-full justify-center shrink-0">
                            Reset
                        </flux:button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $motorcycles->count() }}</span>
                    {{ $motorcycles->count() === 1 ? 'motorcycle' : 'motorcycles' }}.
                </p>
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        @if($motorcycles->isEmpty())
            <div class="text-center py-16 md:py-24 border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-6">
                <p class="text-gray-900 dark:text-white font-semibold text-lg mb-2">No motorcycles match your filters</p>
                <flux:button href="{{ route('sale-motorcycles') }}" variant="outline">View all</flux:button>
            </div>
        @else
            <ul class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 md:gap-8 list-none p-0 m-0 w-full max-w-full" role="list">
                @foreach($motorcycles as $motorcycle)
                    @php
                        $fullPath = \App\Support\NgnMotorcycleImage::urlForNewStock($motorcycle->file_path ?: ($motorcycle->image ?? null));
                    @endphp
                    <li class="min-w-0 w-full max-w-full" x-data="{ open: false }">
                        <flux:card class="group flex h-full w-full max-w-full flex-col overflow-hidden p-0 border-0 ring-1 ring-gray-200/80 dark:ring-gray-700 bg-white dark:bg-gray-900 shadow-md shadow-gray-900/5 dark:shadow-none hover:shadow-xl hover:ring-brand-red/40 transition-all duration-300">
                            <a href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" class="relative block w-full max-w-full overflow-hidden aspect-[5/3] min-h-[11rem] bg-gray-100 dark:bg-gray-800 outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-inset">
                                <img
                                    src="{{ $fullPath }}"
                                    alt="{{ $motorcycle->make }} {{ $motorcycle->model }}"
                                    width="500"
                                    height="300"
                                    decoding="async"
                                    class="absolute inset-0 h-full w-full max-w-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                                    loading="lazy"
                                >
                                <span class="absolute left-3 top-3 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white bg-emerald-700">
                                    New
                                </span>
                            </a>
                            <div class="flex flex-1 flex-col p-4 md:p-5">
                                <h2 class="font-bold text-gray-900 dark:text-white text-base md:text-lg leading-snug">
                                    <a href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" class="hover:text-brand-red transition-colors focus:outline-none focus-visible:underline">
                                        {{ $motorcycle->make }} {{ $motorcycle->model }}
                                    </a>
                                </h2>
                                <p class="mt-1 text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                    {{ $motorcycle->type ?: 'Motorcycle' }}
                                    @if($motorcycle->engine)<span class="text-gray-400 dark:text-gray-500"> · {{ $motorcycle->engine }}</span>@endif
                                    @if($motorcycle->year)<span class="text-gray-400 dark:text-gray-500"> · {{ $motorcycle->year }}</span>@endif
                                </p>
                                <p class="mt-3 text-2xl md:text-3xl font-bold text-brand-red tabular-nums tracking-tight">
                                    @if($motorcycle->sale_new_price)
                                        £{{ number_format((float) $motorcycle->sale_new_price, 0) }}
                                    @else
                                        <span class="text-base text-gray-500 font-normal">Call for price</span>
                                    @endif
                                </p>
                                <div class="mt-2">
                                    <button type="button" @click="open = !open" class="text-xs font-semibold tracking-wide uppercase text-gray-600 dark:text-gray-300">
                                        <span x-show="!open">Show details</span>
                                        <span x-show="open" x-cloak>Hide details</span>
                                    </button>
                                </div>
                                <dl
                                    x-show="open"
                                    x-cloak
                                    class="mt-2 grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] text-gray-600 dark:text-gray-300"
                                >
                                    <div>
                                        <dt class="font-semibold">Category</dt>
                                        <dd>{{ $motorcycle->category ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold">Colour</dt>
                                        <dd>{{ $motorcycle->colour ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold">Fuel</dt>
                                        <dd>{{ $motorcycle->fuel_type ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold">Registration</dt>
                                        <dd class="font-mono tabular-nums">{{ $motorcycle->registration ?? '—' }}</dd>
                                    </div>
                                </dl>
                                <div class="mt-auto pt-4 flex flex-col sm:flex-row gap-2">
                                    <flux:button href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" variant="outline" size="sm" class="flex-1 justify-center ring-1 ring-gray-300 dark:ring-gray-600">
                                        Full details
                                    </flux:button>
                                    <flux:button href="/finance?source=new-bike&bike_id={{ $motorcycle->id }}&bike_type=new&price={{ (float) ($motorcycle->sale_new_price ?? 0) }}" variant="filled" size="sm" class="flex-1 justify-center bg-brand-red text-white hover:bg-brand-red-dark">
                                        Finance
                                    </flux:button>
                                </div>
                            </div>
                        </flux:card>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
