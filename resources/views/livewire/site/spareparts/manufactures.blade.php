<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Manufacturers</h1>
        <a href="{{ route('spareparts.index') }}" class="text-sm text-brand-red hover:underline">Back to finder</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($manufacturers as $manufacturer)
            <a href="{{ route('spareparts.index') }}"
               class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-red transition">
                <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $manufacturer['name'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Start browse flow</p>
            </a>
        @endforeach
    </div>
</div>
