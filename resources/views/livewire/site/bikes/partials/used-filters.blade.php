@props([
    'total' => 0,
    'showPrice' => true,
])

<div class="bike-list-filters space-y-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-4">
        <div class="bike-filter-field sm:col-span-2 lg:col-span-4">
            <label for="bike-search" class="bike-filter-label">Search</label>
            <input
                id="bike-search"
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="Make, model or registration"
                class="bike-filter-control"
                autocomplete="off"
            >
        </div>
        <div class="bike-filter-field lg:col-span-2">
            <label for="bike-sort" class="bike-filter-label">Sort</label>
            <select id="bike-sort" wire:model.live="sort" class="bike-filter-control bike-filter-select">
                <option value="default">Newest listed</option>
                <option value="price_asc">Price: low to high</option>
                <option value="price_desc">Price: high to low</option>
                <option value="year_asc">Year: oldest first</option>
                <option value="year_desc">Year: newest first</option>
            </select>
        </div>
        <div class="bike-filter-field lg:col-span-2">
            <label for="bike-stock" class="bike-filter-label">Stock</label>
            <select id="bike-stock" wire:model.live="availability" class="bike-filter-control bike-filter-select">
                <option value="available">For sale</option>
                <option value="sold">Sold</option>
                <option value="all">All stock</option>
            </select>
        </div>
        @if ($showPrice)
            <div class="bike-filter-field lg:col-span-2">
                <span class="bike-filter-label">Price (£)</span>
                <div class="bike-filter-price-row">
                    <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="Min" min="0" class="bike-filter-control min-w-0">
                    <span class="bike-filter-price-sep" aria-hidden="true">–</span>
                    <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="Max" min="0" class="bike-filter-control min-w-0">
                </div>
            </div>
        @endif
        <div class="bike-filter-field lg:col-span-2">
            <span class="bike-filter-label invisible select-none" aria-hidden="true">Reset</span>
            <button type="button" wire:click="resetFilters" class="bike-filter-btn bike-filter-btn-muted w-full">
                Reset
            </button>
        </div>
    </div>
    <p class="text-xs text-gray-500 dark:text-gray-400">
        Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ number_format($total) }}</span>
        {{ $total === 1 ? 'motorcycle' : 'motorcycles' }}
        @if($availability === 'sold')
            marked as sold.
        @elseif($availability === 'all')
            in stock (for sale and sold).
        @else
            currently for sale.
        @endif
    </p>
</div>
