<div class="space-y-4">
    {{-- Current pricing card --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Current Pricing</h2>
        </div>
        <div class="p-5">
            @if($current)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Weekly Price</p>
                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format($current->weekly_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Minimum Deposit</p>
                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format($current->minimum_deposit, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Updated</p>
                        <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $current->update_date ? $current->update_date->format('d M Y') : '—' }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No current pricing set.</p>
            @endif
        </div>
    </div>

    {{-- History table --}}
    @if($history->count())
        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Pricing History</h2>
            </div>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Weekly Price</flux:table.column>
                    <flux:table.column>Minimum Deposit</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($history as $item)
                        <flux:table.row wire:key="price-{{ $item->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">£{{ number_format($item->weekly_price, 2) }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($item->minimum_deposit, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $item->update_date ? $item->update_date->format('d M Y') : '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif
</div>
