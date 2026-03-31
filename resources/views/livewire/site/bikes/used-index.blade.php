<div>
    <section class="relative bg-gray-900 text-white overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900 to-brand-red/25 opacity-90" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <flux:badge color="red" class="mb-4 uppercase tracking-widest text-[10px]">Used stock</flux:badge>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight">Used motorcycles for sale</h1>
            <p class="mt-3 max-w-2xl text-sm md:text-base text-gray-300 leading-relaxed">
                Same listing source as our homepage: current rental-fleet and trade stock listed for sale. Filter by price or year, or search by make, model or registration.
            </p>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            <form method="GET" action="{{ route('used-motorcycles.page') }}" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-4 items-end">
                    <div class="sm:col-span-2 lg:col-span-4 w-full">
                        <label for="used-search" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                        <input
                            id="used-search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Make, model or registration"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-transparent"
                        >
                    </div>
                    <div class="w-full lg:col-span-3">
                        <label for="used-sort" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Sort</label>
                        <select
                            id="used-sort"
                            name="sort"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                        >
                            <option value="default" @selected($sort === 'default')>Newest listed</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                            <option value="year_asc" @selected($sort === 'year_asc')>Year: oldest first</option>
                            <option value="year_desc" @selected($sort === 'year_desc')>Year: newest first</option>
                        </select>
                    </div>
                    <div class="w-full lg:col-span-3">
                        <label for="used-availability" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Stock</label>
                        <select
                            id="used-availability"
                            name="availability"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                        >
                            <option value="available" @selected($availability === 'available' || $availability === 'all')>For sale</option>
                            <option value="sold" @selected($availability === 'sold')>Sold</option>
                        </select>
                    </div>
                    <div class="w-full lg:col-span-2 flex gap-2">
                        <flux:button type="submit" variant="filled" class="w-full justify-center bg-brand-red text-white hover:bg-brand-red-dark">
                            Apply
                        </flux:button>
                        <flux:button href="{{ route('used-motorcycles.page') }}" variant="outline" class="w-full justify-center shrink-0">
                            Reset
                        </flux:button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $motorbikes->count() }}</span>
                    {{ $motorbikes->count() === 1 ? 'motorcycle' : 'motorcycles' }}
                    @if($availability === 'sold')
                        marked as sold.
                    @else
                        currently for sale.
                    @endif
                </p>
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        @if($motorbikes->isEmpty())
            <div class="text-center py-16 md:py-24 border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-6">
                <flux:icon name="magnifying-glass" class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" />
                <p class="text-gray-900 dark:text-white font-semibold text-lg mb-2">No motorcycles match your filters</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">Try clearing the search or switching stock to “For sale”.</p>
                <flux:button href="{{ route('used-motorcycles.page') }}" variant="outline">View all for sale</flux:button>
            </div>
        @else
            <ul class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-8 list-none p-0 m-0 w-full max-w-full" role="list">
                @foreach($motorbikes as $bike)
                    @php
                        $image = $bike->image_one
                            ? 'https://neguinhomotors.co.uk/storage/motorbikes/'.$bike->image_one
                            : 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                        $isSold = (int) $bike->is_sold === 1;
                        $regHint = $bike->reg_no ? '••••'.substr((string) $bike->reg_no, -3) : '';
                    @endphp
                    <li class="min-w-0 w-full max-w-full">
                        <flux:card class="group flex h-full w-full max-w-full flex-col overflow-hidden p-0 border-0 ring-1 ring-gray-200/80 dark:ring-gray-700 bg-white dark:bg-gray-900 shadow-md shadow-gray-900/5 dark:shadow-none hover:shadow-xl hover:ring-brand-red/40 transition-all duration-300">
                            <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="relative block w-full max-w-full overflow-hidden aspect-[5/3] min-h-[11rem] bg-gray-100 dark:bg-gray-800 outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-inset">
                                <img
                                    src="{{ $image }}"
                                    alt="{{ $bike->make }} {{ $bike->model }}"
                                    width="500"
                                    height="300"
                                    decoding="async"
                                    class="absolute inset-0 h-full w-full max-w-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                                    loading="lazy"
                                >
                                <span class="absolute left-3 top-3 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white {{ $isSold ? 'bg-gray-800' : 'bg-emerald-700' }}">
                                    {{ $isSold ? 'Sold' : 'For sale' }}
                                </span>
                            </a>
                            <div class="flex flex-1 flex-col p-4 md:p-5">
                                <h2 class="font-bold text-gray-900 dark:text-white text-base md:text-lg leading-snug">
                                    <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="hover:text-brand-red transition-colors focus:outline-none focus-visible:underline">
                                        {{ $bike->make }} {{ $bike->model }}
                                    </a>
                                </h2>
                                <p class="mt-1 text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                    @if($regHint)<span class="font-mono tabular-nums">{{ $regHint }}</span> · @endif
                                    {{ $bike->year ?? '—' }}
                                    @if($bike->engine)<span class="text-gray-400 dark:text-gray-500"> · {{ $bike->engine }}</span>@endif
                                </p>
                                <p class="mt-3 text-2xl md:text-3xl font-bold text-brand-red tabular-nums tracking-tight">
                                    £{{ number_format((float) $bike->price, 0) }}
                                </p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Guide price — confirm on enquiry</p>
                                <div class="mt-auto pt-4 flex flex-col sm:flex-row gap-2">
                                    <flux:button href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" variant="outline" size="sm" class="flex-1 justify-center ring-1 ring-gray-300 dark:ring-gray-600">
                                        Details
                                    </flux:button>
                                    <flux:button href="/finance?source=used-bike&bike_id={{ $bike->id }}&bike_type=used&price={{ (float) $bike->price }}" variant="filled" size="sm" class="flex-1 justify-center bg-brand-red text-white hover:bg-brand-red-dark">
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

    @if($latestMotorcycles->isNotEmpty())
        <section class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">New bikes in stock</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Honda &amp; Yamaha — enquire for delivery and finance.</p>
                    </div>
                    <flux:button href="{{ route('site.bikes') }}" variant="outline" size="sm" class="self-start sm:self-auto">View new range</flux:button>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 list-none p-0 m-0 w-full max-w-full" role="list">
                    @foreach($latestMotorcycles as $motorcycle)
                        @php
                            $nmImage = $motorcycle->file_path
                                ? 'https://neguinhomotors.co.uk'.$motorcycle->file_path
                                : 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                        @endphp
                        <li class="min-w-0 w-full max-w-full">
                            <flux:card class="overflow-hidden p-0 border-0 ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 h-full w-full max-w-full flex flex-col">
                                <a href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" class="relative block w-full max-w-full overflow-hidden aspect-[5/3] min-h-[11rem] bg-gray-100 dark:bg-gray-800">
                                    <img
                                        src="{{ $nmImage }}"
                                        alt="{{ $motorcycle->make }} {{ $motorcycle->model }}"
                                        width="500"
                                        height="300"
                                        decoding="async"
                                        class="absolute inset-0 h-full w-full max-w-full object-cover object-center hover:opacity-95 transition-opacity"
                                        loading="lazy"
                                    >
                                </a>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $motorcycle->make }} {{ $motorcycle->model }}</h3>
                                    <p class="text-brand-red font-bold text-lg mt-2">£{{ number_format((float) $motorcycle->sale_new_price, 0) }}</p>
                                    <flux:button href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" variant="outline" size="sm" class="w-full mt-auto justify-center">Enquire</flux:button>
                                </div>
                            </flux:card>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</div>
