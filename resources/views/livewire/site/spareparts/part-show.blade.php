<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Part number</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $part['part_number'] }}</h1>
            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $part['name'] }}</p>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div class="border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stock</p>
                    <p class="font-semibold mt-1">{{ $part['stock'] }}</p>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">RRP</p>
                    <p class="font-semibold mt-1">£{{ number_format((float) $part['price_gbp_inc_vat'], 2) }}</p>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Qty used</p>
                    <p class="font-semibold mt-1">{{ $part['qty_used'] }}</p>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">VAT</p>
                    <p class="font-semibold mt-1">inc.</p>
                </div>
            </div>

            @if(!empty($part['note']))
                <div class="mt-4 border border-amber-300/60 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-900/20 p-3 text-xs text-amber-800 dark:text-amber-200">
                    {{ $part['note'] }}
                </div>
            @endif
        </section>

        <aside class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="font-bold text-gray-900 dark:text-white">Buy now</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add to shared basket and checkout with your normal order flow.</p>
            <div class="mt-4 space-y-2">
                <flux:button wire:click="addToBasket" variant="filled" class="w-full">Add to basket</flux:button>
                <a href="{{ route('spareparts.cart') }}" class="inline-flex w-full justify-center border border-gray-300 dark:border-gray-600 py-2 text-sm hover:border-brand-red">View basket</a>
                <a href="{{ route('spareparts.index') }}" class="inline-flex w-full justify-center text-sm text-brand-red hover:underline">Back to spareparts</a>
            </div>
        </aside>
    </div>

    <section class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-x-auto">
        <div class="p-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="font-bold text-gray-900 dark:text-white">Bikes that use this part</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $part['part_number'] }}</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="p-2 text-left">Bike fitment</th>
                    <th class="p-2 text-left">Assembly</th>
                    <th class="p-2 text-left"></th>
                </tr>
            </thead>
            <tbody>
                @foreach(($part['fitments'] ?? []) as $fitment)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="p-2">
                            {{ $fitment['manufacturer'] }}, {{ $fitment['model'] }}, {{ $fitment['year'] }}, {{ $fitment['country'] }}, {{ $fitment['colour'] }}
                        </td>
                        <td class="p-2">{{ $fitment['assembly'] }}</td>
                        <td class="p-2 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="addToBasket('{{ $fitment['assembly_slug'] }}')" size="sm" variant="ghost">Add</flux:button>
                                <a class="inline-flex items-center text-xs text-brand-red hover:underline"
                                   href="{{ route('spareparts.assembly', ['manufacturer'=>$fitment['manufacturer_slug'],'model'=>$fitment['model_slug'],'year'=>$fitment['year'],'country'=>$fitment['country_slug'],'colour'=>$fitment['colour_slug'],'assembly'=>$fitment['assembly_slug']]) }}">
                                    Open assembly
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
