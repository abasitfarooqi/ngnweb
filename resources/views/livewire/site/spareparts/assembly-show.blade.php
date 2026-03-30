<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <section class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 border border-slate-700">
            <h1 class="text-2xl md:text-3xl font-bold uppercase">{{ str_replace('-', ' ', $assembly) }}</h1>
            <p class="text-xs text-slate-300 mt-2">
                {{ strtoupper($manufacturer) }} -> {{ strtoupper($model) }} -> {{ $year }} -> {{ strtoupper(str_replace('-', ' ', $country)) }} -> {{ strtoupper(str_replace('-', ' ', $colour)) }}
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('spareparts.index') }}" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5">Back to finder</a>
                <a href="{{ route('spareparts.cart') }}" class="text-xs bg-brand-red hover:bg-red-700 px-3 py-1.5">View basket</a>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
            <aside class="xl:col-span-2 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Assemblies</h2>
                </div>
                <div class="p-3 border-b border-gray-200 dark:border-gray-800">
                    <input type="search" wire:model.live.debounce.250ms="assemblySearch" placeholder="Filter assemblies..." class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2">
                </div>
                <div class="max-h-[520px] overflow-auto">
                    @foreach($this->assemblies as $assemblyOption)
                        <a href="{{ route('spareparts.assembly', ['manufacturer'=>$manufacturer,'model'=>$model,'year'=>$year,'country'=>$country,'colour'=>$colour,'assembly'=>$assemblyOption['slug']]) }}"
                           class="block px-3 py-2 border-b border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800 {{ $assembly === $assemblyOption['slug'] ? 'bg-slate-900 text-white' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $assemblyOption['name'] }}
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="xl:col-span-3 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Parts in this assembly</h2>
                </div>
                <div class="p-3 text-xs text-amber-700 dark:text-amber-300 border-b border-gray-200 dark:border-gray-800 bg-amber-50/60 dark:bg-amber-900/20">
                    Please note: For colour dependent parts that have no colour specified, contact us before ordering.
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800">
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
                            @foreach($parts as $index => $part)
                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="p-2">{{ $index + 1 }}</td>
                                    <td class="p-2">
                                        <a class="font-semibold text-gray-900 dark:text-white hover:text-brand-red" href="{{ route('spareparts.part', ['partNumber' => $part['part_number']]) }}">{{ $part['name'] }}</a>
                                        <div class="text-xs text-gray-500">{{ $part['part_number'] }}</div>
                                        @if(!empty($part['note']))
                                            <div class="text-xs text-amber-600 mt-1">{{ $part['note'] }}</div>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <span class="text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">{{ $part['stock'] }}</span>
                                    </td>
                                    <td class="p-2 font-semibold text-gray-900 dark:text-white">£{{ number_format((float) $part['price_gbp_inc_vat'], 2) }}<div class="text-xs font-normal text-gray-500">inc. VAT</div></td>
                                    <td class="p-2">{{ $part['qty_used'] }}</td>
                                    <td class="p-2">
                                        <flux:button wire:click="addToBasket('{{ $part['part_number'] }}')" variant="filled" size="sm">Add</flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
