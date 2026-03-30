<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <section class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 md:p-8 border border-slate-700">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold tracking-tight">OEM Spareparts</h1>
                    <p class="text-sm md:text-base text-slate-300 mt-2">Search by part number, browse fitments, or shop the full spareparts catalogue.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('spareparts.cart') }}" class="inline-flex items-center bg-brand-red hover:bg-red-700 px-4 py-2 text-sm font-semibold">Basket ({{ $this->basketCount }})</a>
                </div>
            </div>
        </section>

        <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 md:p-5 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Search by Part Number</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">Fast direct lookup</span>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" wire:model.defer="partNumberSearch" placeholder="Enter part number, e.g. 06451961405" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                <flux:button wire:click="searchPartNumber" variant="filled">Search</flux:button>
                <flux:button wire:click="clearPartNumberSearch" variant="ghost">Clear</flux:button>
            </div>
            @if($partLookup)
                <div class="border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('spareparts.part', ['partNumber' => $partLookup['part_number']]) }}" class="font-semibold text-gray-900 dark:text-white hover:text-brand-red">
                                {{ $partLookup['name'] }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $partLookup['part_number'] }}</p>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">£{{ number_format((float) $partLookup['price_gbp_inc_vat'], 2) }}</span>
                    </div>
                    @if(!empty($partLookup['note']))
                        <p class="text-xs text-amber-600 mt-2">{{ $partLookup['note'] }}</p>
                    @endif
                </div>
            @endif
        </section>

        <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 md:p-5 space-y-4">
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Spareparts Catalogue</h2>
                <button type="button" wire:click="clearCatalogueFilters" class="text-xs text-gray-500 hover:text-brand-red">Reset filters</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                <input wire:model.live.debounce.300ms="catalogueSearch" type="search" placeholder="Search part name / number" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                <select wire:model.live="catalogueManufacturer" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                    <option value="">All manufacturers</option>
                    @foreach($this->catalogueManufacturerOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="catalogueModel" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                    <option value="">All models</option>
                    @foreach($this->catalogueModelOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="catalogueCategory" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                    <option value="">All categories</option>
                    @foreach($this->catalogueCategoryOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }} ({{ $option['count'] }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($this->catalogueCards as $card)
                    <article class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex flex-col">
                        <a href="{{ route('spareparts.part', ['partNumber' => $card['part_number']]) }}" class="block">
                            @if($card['image'])
                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" class="w-full h-40 object-contain bg-white dark:bg-gray-900 p-3">
                            @else
                                <div class="w-full h-40 bg-white dark:bg-gray-900 p-3 flex items-center justify-center text-xs text-gray-400">No image</div>
                            @endif
                        </a>
                        <div class="p-3 flex-1 flex flex-col">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $card['manufacturer_name'] }} {{ $card['model_name'] }}</p>
                            <a href="{{ route('spareparts.part', ['partNumber' => $card['part_number']]) }}" class="mt-1 text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red line-clamp-2">
                                {{ $card['name'] }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">{{ $card['part_number'] }}</p>
                            <p class="text-xs text-gray-500">{{ $card['category_name'] }}</p>
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">£{{ number_format($card['price'], 2) }}</span>
                                <span class="text-[10px] px-2 py-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300">{{ $card['stock'] }}</span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <flux:button wire:click="addCataloguePartToBasket({{ $card['id'] }})" variant="filled" size="sm">Add</flux:button>
                                <a href="{{ route('spareparts.part', ['partNumber' => $card['part_number']]) }}" class="inline-flex items-center justify-center text-xs border border-gray-300 dark:border-gray-600 hover:border-brand-red text-gray-700 dark:text-gray-200 px-2 py-1.5">Details</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-8 text-sm text-gray-500 dark:text-gray-400">No parts matched your filters.</div>
                @endforelse
            </div>
            <div class="flex justify-center">
                <flux:button wire:click="loadMoreCatalogue" variant="ghost">Load more</flux:button>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-5 gap-4">
            <div class="xl:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Browse for Parts</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3">Manufacturer -> Model -> Year -> Country -> Colour -> Assembly</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <select wire:model.live="selectedManufacturer" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                        <option value="">Select Manufacturer</option>
                        @foreach($this->manufacturerOptions as $option)
                            <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedModel" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2" @disabled($selectedManufacturer === '')>
                        <option value="">Select Model</option>
                        @foreach($this->modelOptions as $option)
                            <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedYear" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2" @disabled($selectedModel === '')>
                        <option value="">Select Year</option>
                        @foreach($this->yearOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedCountry" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2" @disabled($selectedYear === '')>
                        <option value="">Select Country</option>
                        @foreach($this->countryOptions as $option)
                            <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedColour" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2 sm:col-span-2" @disabled($selectedCountry === '')>
                        <option value="">Select Colour</option>
                        @foreach($this->colourOptions as $option)
                            <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="xl:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="bg-slate-900 text-white px-3 py-2 text-sm">Assemblies</div>
                    <div class="p-3 border-b border-gray-200 dark:border-gray-800">
                        <input type="search" wire:model.live.debounce.250ms="assemblySearch" placeholder="Filter assemblies" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                    </div>
                    <div class="max-h-[360px] overflow-auto">
                        @forelse($this->assemblyOptions as $assembly)
                            <button type="button" wire:click="selectAssembly('{{ $assembly['slug'] }}')" class="w-full text-left px-3 py-2 border-b border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800 {{ $selectedAssembly === $assembly['slug'] ? 'bg-slate-900 text-white' : '' }}">
                                {{ $assembly['name'] }}
                            </button>
                        @empty
                            <div class="p-3 text-sm text-gray-500">Complete filters to load assemblies.</div>
                        @endforelse
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="bg-slate-900 text-white px-3 py-2 text-sm">Assembly Parts</div>
                    <div class="max-h-[360px] overflow-auto divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($this->assemblyParts as $part)
                            <div class="p-3">
                                <a href="{{ route('spareparts.part', ['partNumber' => $part['part_number']]) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-red">{{ $part['name'] }}</a>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $part['part_number'] }}</p>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <span class="text-sm font-bold">£{{ number_format((float) $part['price_gbp_inc_vat'], 2) }}</span>
                                    <flux:button wire:click="addToBasket('{{ $part['part_number'] }}')" size="sm" variant="filled">Add</flux:button>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-sm text-gray-500">Select an assembly to view parts.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
