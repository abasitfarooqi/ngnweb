<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">NGN Shop</h1>
        <p class="text-gray-300 text-lg">Accessories, helmets, clothing, spare parts & more</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-12">
        @foreach([
            ['title'=>'Accessories', 'href'=>'/shop/accessories', 'icon'=>'puzzle-piece', 'text'=>'Phone mounts, covers, heated grips & more'],
            ['title'=>'Helmets', 'href'=>'/helmets', 'icon'=>'shield-check', 'text'=>'MT, Simpson, HJC & more top brands'],
            ['title'=>'Spare Parts', 'href'=>'/shop/spare-parts', 'icon'=>'wrench-screwdriver', 'text'=>'Honda & Yamaha genuine & aftermarket parts'],
            ['title'=>'GPS Trackers', 'href'=>'/shop/gps-tracker', 'icon'=>'map-pin', 'text'=>'Track & protect your motorcycle'],
        ] as $cat)
            <a href="{{ $cat['href'] }}" class="group block">
                <flux:card class="p-6 text-center h-full hover:border-brand-red transition">
                    <div class="w-14 h-14 bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red transition">
                        <flux:icon name="{{ $cat['icon'] }}" class="h-7 w-7 text-gray-600 dark:text-gray-400 group-hover:text-white transition" />
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 group-hover:text-brand-red transition">{{ $cat['title'] }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cat['text'] }}</p>
                </flux:card>
            </a>
        @endforeach
    </div>

    {{-- Products --}}
    @if(isset($products) && $products->count() > 0)
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Latest Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($products as $product)
                <a href="{{ route('ngn_product_details', $product->sku) }}" class="group block">
                    <flux:card class="overflow-hidden p-0">
                        <img src="{{ $product->image_url ?? asset('assets/img/no-image.png') }}"
                             alt="{{ $product->name }}"
                             class="w-full h-36 object-cover group-hover:opacity-90 transition">
                        <div class="p-3">
                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate group-hover:text-brand-red transition">{{ $product->name }}</p>
                        </div>
                    </flux:card>
                </a>
            @endforeach
        </div>
    @endif
</div>
</div>
