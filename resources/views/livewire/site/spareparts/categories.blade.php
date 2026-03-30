<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Spareparts Categories</h1>
        <a href="{{ route('spareparts.index') }}" class="text-sm text-brand-red hover:underline">Back to finder</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($categories as $category)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $category->count_rows }} assemblies</p>
            </div>
        @empty
            <div class="col-span-full text-sm text-gray-500">No categories available.</div>
        @endforelse
    </div>
</div>
