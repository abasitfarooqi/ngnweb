<div class="bg-slate-100 dark:bg-slate-950 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-slate-900 text-white px-5 py-4 rounded-none">
            <h1 class="text-2xl md:text-3xl font-bold">Find Your Bike Part</h1>
            <p class="text-sm text-slate-300 mt-1">Two ways to find parts: search by part number or browse by bike filters.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-none space-y-3">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Search by Part Number</h2>
                <div class="flex gap-2">
                    <input
                        type="text"
                        wire:model.defer="partNumberSearch"
                        placeholder="Enter part number"
                        class="w-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none"
                    >
                    <flux:button wire:click="searchPartNumber" variant="filled" class="rounded-none">Search</flux:button>
                    <flux:button wire:click="clearPartNumberSearch" variant="ghost" class="rounded-none">Clear</flux:button>
                </div>
                @if($partLookup)
                    <div class="border border-slate-300 dark:border-slate-700 p-3 text-sm rounded-none">
                        <div class="font-semibold">{{ $partLookup['name'] }} <span class="text-slate-500">({{ $partLookup['part_number'] }})</span></div>
                        @if(!empty($partLookup['note']))
                            <div class="mt-1 text-amber-600">{{ $partLookup['note'] }}</div>
                        @endif
                        <div class="mt-1">Stock: <strong>{{ $partLookup['stock'] }}</strong></div>
                        <div>Price: <strong>£{{ number_format($partLookup['price_gbp_inc_vat'], 2) }}</strong> inc. VAT</div>
                        <div class="mt-3">
                            <div class="font-semibold mb-1">Bikes that use this part</div>
                            <div class="max-h-48 overflow-auto border border-slate-200 dark:border-slate-800 rounded-none">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-100 dark:bg-slate-800">
                                        <tr>
                                            <th class="text-left p-2">Bike</th>
                                            <th class="text-left p-2">Assembly</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($partLookup['fitments'] ?? []) as $fitment)
                                            <tr class="border-t border-slate-200 dark:border-slate-800">
                                                <td class="p-2">{{ $fitment['manufacturer'] }}, {{ $fitment['model'] }}, {{ $fitment['year'] }}, {{ $fitment['country'] }}, {{ $fitment['colour'] }}</td>
                                                <td class="p-2">{{ $fitment['assembly'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-none">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Basket</h2>
                <div class="text-sm text-slate-600 dark:text-slate-300 mt-1">Items: {{ $this->basketCount }}</div>
                @if(count($basket) > 0)
                    <div class="mt-3 space-y-2 max-h-48 overflow-auto">
                        @foreach($basket as $item)
                            <div class="border border-slate-200 dark:border-slate-800 p-2 text-sm rounded-none">
                                <div class="font-semibold">{{ $item['name'] }}</div>
                                <div>{{ $item['part_number'] }} · Qty {{ $item['quantity'] }} · £{{ number_format((float) $item['price_gbp_inc_vat'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <flux:button wire:click="clearBasket" variant="danger" class="rounded-none">Clear Basket</flux:button>
                    </div>
                @else
                    <p class="text-sm text-slate-500 mt-3">No parts added yet.</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-none p-4">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-3">Browse for Parts</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                Select Manufacturer, then Model, Year, Country, Colour and finally Assembly.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
                <select wire:model.live="selectedManufacturer" class="border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none">
                    <option value="">Select Manufacturer</option>
                    @foreach($this->manufacturerOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedModel" class="border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none" @disabled($selectedManufacturer === '')>
                    <option value="">Select Model</option>
                    @foreach($this->modelOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedYear" class="border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none" @disabled($selectedModel === '')>
                    <option value="">Select Year</option>
                    @foreach($this->yearOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedCountry" class="border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none" @disabled($selectedYear === '')>
                    <option value="">Select Country</option>
                    @foreach($this->countryOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedColour" class="border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none" @disabled($selectedCountry === '')>
                    <option value="">Select Colour</option>
                    @foreach($this->colourOptions as $option)
                        <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1 border border-slate-200 dark:border-slate-800 rounded-none">
                    <div class="bg-slate-900 text-white px-3 py-2 text-sm">Assemblies</div>
                    <div class="p-3 border-b border-slate-200 dark:border-slate-800">
                        <input type="search" wire:model.live.debounce.250ms="assemblySearch" placeholder="Filter..." class="w-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 rounded-none">
                    </div>
                    <div class="max-h-[420px] overflow-auto">
                        @forelse($this->assemblyOptions as $assembly)
                            <button
                                type="button"
                                wire:click="selectAssembly('{{ $assembly['slug'] }}')"
                                class="w-full text-left px-3 py-2 border-b border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 {{ $selectedAssembly === $assembly['slug'] ? 'bg-slate-900 text-white' : '' }}"
                            >
                                {{ $assembly['name'] }}
                            </button>
                        @empty
                            <div class="p-3 text-sm text-slate-500">No assemblies. Complete filters first.</div>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-2 border border-slate-200 dark:border-slate-800 rounded-none">
                    <div class="bg-slate-900 text-white px-3 py-2 text-sm">Parts List</div>
                    <div class="p-3 text-xs text-amber-600 border-b border-slate-200 dark:border-slate-800">
                        Please note: For colour dependent parts with no colour specified, contact us before ordering.
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 dark:bg-slate-800">
                                <tr>
                                    <th class="text-left p-2">#</th>
                                    <th class="text-left p-2">Part</th>
                                    <th class="text-left p-2">Stock</th>
                                    <th class="text-left p-2">RRP</th>
                                    <th class="text-left p-2">Qty Used</th>
                                    <th class="text-left p-2">Basket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->assemblyParts as $index => $part)
                                    <tr class="border-t border-slate-200 dark:border-slate-800">
                                        <td class="p-2">{{ $index + 1 }}</td>
                                        <td class="p-2">
                                            <div class="font-semibold">{{ $part['name'] }}</div>
                                            <div class="text-xs text-slate-500">{{ $part['part_number'] }}</div>
                                            @if(!empty($part['note']))
                                                <div class="text-xs text-amber-600 mt-1">{{ $part['note'] }}</div>
                                            @endif
                                        </td>
                                        <td class="p-2">{{ $part['stock'] }}</td>
                                        <td class="p-2">£{{ number_format((float) $part['price_gbp_inc_vat'], 2) }}<div class="text-xs text-slate-500">inc. VAT</div></td>
                                        <td class="p-2">{{ $part['qty_used'] }}</td>
                                        <td class="p-2">
                                            <flux:button wire:click="addToBasket('{{ $part['part_number'] }}')" variant="filled" class="rounded-none">Add</flux:button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-slate-500">Select an assembly to view parts.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
