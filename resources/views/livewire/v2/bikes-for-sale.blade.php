<div>
    {{-- Page header --}}
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-black text-white">Motorcycles for Sale</h1>
            <p class="text-zinc-400 mt-2 text-sm">Browse our current stock of new and used motorcycles in London.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <input
                wire:model.live.debounce.400ms="search"
                type="text"
                placeholder="Search by make, model or reg..."
                class="ngn-input flex-1"
            >
            <select wire:model.live="condition" class="ngn-input sm:w-44">
                <option value="">All Conditions</option>
                <option value="new">New</option>
                <option value="used">Used</option>
            </select>
            <select wire:model.live="sort" class="ngn-input sm:w-44">
                <option value="latest">Newest First</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
            </select>
        </div>

        {{-- Loading overlay --}}
        <div wire:loading.class="opacity-50 pointer-events-none">
            {{-- Grid --}}
            @if($bikes->isEmpty())
                <div class="text-center py-20 text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-lg font-semibold">No bikes found</p>
                    <p class="text-sm mt-1">Try adjusting your search or filters.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bikes as $sale)
                        @php
                            $mb = $sale->getMotorbike();
                            $img = $sale->getMotorbikeImage();
                        @endphp
                        @if($mb)
                        <a href="{{ route('v2.bike.detail', $mb->slug ?? $mb->id) }}" class="ngn-bike-card group">
                            <div class="overflow-hidden bg-zinc-100">
                                @if($img)
                                    <img src="{{ Storage::url($img->image_path ?? '') }}"
                                         alt="{{ $mb->make }} {{ $mb->model }}"
                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center bg-zinc-800 text-zinc-500 text-sm">No image</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h2 class="font-bold text-zinc-900 text-base leading-tight">{{ $mb->make }} {{ $mb->model }}</h2>
                                    @if($sale->condition)
                                        <span class="ngn-badge-{{ $sale->condition === 'new' ? 'orange' : 'zinc' }} flex-shrink-0">{{ ucfirst($sale->condition) }}</span>
                                    @endif
                                </div>
                                <p class="text-zinc-500 text-xs mb-3">{{ $mb->year }} &bull; {{ $mb->engine }}cc &bull; {{ $mb->color }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-orange-600 font-black text-xl">&pound;{{ number_format($sale->price) }}</span>
                                    <span class="text-xs text-orange-600 font-semibold">View Details &rarr;</span>
                                </div>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $bikes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
