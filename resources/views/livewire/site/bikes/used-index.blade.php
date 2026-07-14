<div>
    <section class="relative site-page-hero overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-brand-red" aria-hidden="true"></div>
        <div class="absolute inset-0 site-page-hero-overlay bg-gradient-to-br from-gray-900 via-gray-900 to-brand-red/25 opacity-90" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <flux:badge class="site-flux-badge-green mb-4 uppercase tracking-widest text-[10px]">Used stock</flux:badge>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight">Used motorcycles for sale</h1>
            <p class="mt-3 max-w-2xl text-sm md:text-base text-gray-300 leading-relaxed">
                Browse our full used stock. Search by make, model or registration, filter sold or for sale, and sort by price or year.
            </p>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            @include('livewire.site.bikes.partials.used-filters', ['total' => $total])
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        @include('livewire.site.bikes.partials.used-bike-grid', [
            'motorbikes' => $motorbikes,
            'hasMore' => $hasMore,
            'infiniteScroll' => true,
        ])
    </section>

    @if($latestMotorcycles->isNotEmpty())
        <section class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">New bikes in stock</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Honda &amp; Yamaha — enquire for delivery and payment plans.</p>
                    </div>
                    <flux:button href="{{ route('sale-motorcycles') }}" variant="outline" size="sm" class="self-start sm:self-auto">View new range</flux:button>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 list-none p-0 m-0 w-full" role="list">
                    @foreach($latestMotorcycles as $motorcycle)
                        @php
                            $nmImage = \App\Support\NgnMotorcycleImage::urlForNewStock($motorcycle->file_path ?: ($motorcycle->image ?? null));
                        @endphp
                        <li class="min-w-0 w-full">
                            <article class="overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 h-full flex flex-col">
                                <a href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" class="relative block aspect-[5/3] min-h-[11rem] bg-gray-100 dark:bg-gray-800">
                                    <img src="{{ $nmImage }}" alt="{{ $motorcycle->make }} {{ $motorcycle->model }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                </a>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $motorcycle->make }} {{ $motorcycle->model }}</h3>
                                    <p class="text-brand-red font-bold text-lg mt-2">
                                        @if($motorcycle->sale_new_price)
                                            £{{ number_format((float) $motorcycle->sale_new_price, 0) }}
                                        @else
                                            <span class="text-sm text-gray-500 font-normal">Call for price</span>
                                        @endif
                                    </p>
                                    <flux:button href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" variant="outline" size="sm" class="w-full mt-auto justify-center">Enquire</flux:button>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</div>
