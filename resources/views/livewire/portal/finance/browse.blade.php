<div wire:key="finance-browse-page">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Finance a Motorbike</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Browse available motorbikes and apply for finance. Spread the cost over 52 weeks with affordable weekly payments.</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Make, model, registration..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <select wire:model.live="branch_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    <option value="">All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition</label>
                <select wire:model.live="condition"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    <option value="">All</option>
                    <option value="0">New</option>
                    <option value="1">Used</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min Deposit</label>
                <input type="number" wire:model.live="minDeposit" placeholder="500" min="0" step="50"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
            </div>
        </div>
    </div>

    {{-- Motorbike Grid --}}
    @if ($motorbikes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach ($motorbikes as $bike)
                @php
                    $estimatedPrice  = isset($bike->sale_price) && $bike->sale_price > 0 ? (float) $bike->sale_price : 5000;
                    $bikeMinDeposit  = max((float) $minDeposit, $estimatedPrice * 0.1);
                    $financeAmount   = max(0, $estimatedPrice - $bikeMinDeposit);
                    $monthlyPayment  = $financeAmount > 0 ? round($financeAmount / 52, 2) : 0;
                @endphp

                <div wire:key="bike-{{ $bike->id }}" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                        @if ($bike->images->count() > 0)
                            <img src="{{ Storage::url($bike->images->first()->path) }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 flex items-center justify-center text-gray-400">No Image</div>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $bike->make }} {{ $bike->model }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $bike->reg_no }}</p>
                        
                        @if ($bike->branch)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">📍 {{ $bike->branch->name }}</p>
                        @endif

                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Est. Price:</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">£{{ number_format($estimatedPrice, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Min Deposit (10%):</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">£{{ number_format($bikeMinDeposit, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">From per month:</span>
                                <span class="text-xl font-bold text-brand-red">£{{ number_format($monthlyPayment, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('account.finance.apply', $bike->id) }}" 
                                class="block w-full text-center px-4 py-2 bg-brand-red text-white font-medium rounded hover:bg-red-700 transition-colors">
                                Apply for Finance
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{-- Pagination not available: $motorbikes is a Collection, not a LengthAwarePaginator --}}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No motorbikes found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria.</p>
        </div>
    @endif

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">How Finance Works</h4>
        <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <li>• Choose your motorbike and apply online</li>
            <li>• Pay a minimum 10% deposit</li>
            <li>• Spread the remaining cost over 52 weeks</li>
            <li>• Affordable weekly or monthly payments</li>
            <li>• Own your bike at the end of the term</li>
        </ul>
    </div>
</div>
