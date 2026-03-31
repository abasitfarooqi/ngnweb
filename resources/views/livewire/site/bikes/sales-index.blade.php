<div>
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold">Motorcycles For Sale</h1>
            <p class="mt-2 text-gray-300">Same stock source as legacy page, upgraded for Livewire + Flux + Tailwind.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="GET" action="{{ route('sale-motorcycles') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input name="search" value="{{ $search }}" placeholder="Search by make or model" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
            <select name="sort" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                <option value="default" @selected($sort === 'default')>Sort by</option>
                <option value="price_asc" @selected($sort === 'price_asc')>Price: Low to High</option>
                <option value="price_desc" @selected($sort === 'price_desc')>Price: High to Low</option>
            </select>
            <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Apply</flux:button>
        </form>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($motorcycles as $motorcycle)
                @php
                    $fullPath = '';
                    if (!empty($motorcycle->file_path) && is_string($motorcycle->file_path)) {
                        if (str_starts_with($motorcycle->file_path, '/storage/uploads/') || str_starts_with($motorcycle->file_path, '/storage/motorbikes/')) {
                            $fullPath = $motorcycle->file_path;
                        } else {
                            $fullPath = '/storage/motorbikes/'.$motorcycle->file_path;
                        }
                    }
                    if ($fullPath === '') {
                        $fullPath = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                    }
                @endphp
                <article class="border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <a href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" class="block">
                        <img src="{{ $fullPath }}" alt="{{ $motorcycle->make }} {{ $motorcycle->model }}" class="w-full h-52 object-cover">
                    </a>
                    <div class="p-4 space-y-2">
                        <p class="text-xs uppercase tracking-wide text-emerald-600">New</p>
                        <h2 class="font-semibold text-gray-900 dark:text-white">{{ $motorcycle->make }} {{ $motorcycle->model }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $motorcycle->type ?: 'Motorcycle' }} · {{ $motorcycle->engine }}cc</p>
                        @if($motorcycle->sale_new_price)
                            <p class="text-brand-red font-bold text-lg">GBP {{ number_format((float) $motorcycle->sale_new_price, 2) }}</p>
                        @endif
                        <div class="flex gap-2">
                            <flux:button href="{{ route('new-motorcycle.detail', ['id' => $motorcycle->id]) }}" variant="outline" size="sm" class="w-full">More information</flux:button>
                            <flux:button href="/finance?source=new-bike&bike_id={{ $motorcycle->id }}&bike_type=new&price={{ (float) ($motorcycle->sale_new_price ?? 0) }}" variant="outline" size="sm" class="w-full">Finance</flux:button>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-gray-600 dark:text-gray-300">No motorcycles found.</p>
            @endforelse
        </div>
    </section>
</div>
