<div>
    @if($extras->isEmpty())
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            No extra items found for this application.
        </div>
    @else
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Price</flux:table.column>
                    <flux:table.column>Quantity</flux:table.column>
                    <flux:table.column>Total</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($extras as $extra)
                        <flux:table.row wire:key="ei-{{ $extra->id }}">
                            <flux:table.cell>{{ $extra->name }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($extra->price ?? 0, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $extra->quantity }}</flux:table.cell>
                            <flux:table.cell class="font-semibold">£{{ number_format($extra->total ?? 0, 2) }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-end">
            <div class="text-sm">
                <span class="text-zinc-500 dark:text-zinc-400">Grand Total:</span>
                <span class="font-semibold text-zinc-900 dark:text-white ml-2">£{{ number_format($extras->sum('total'), 2) }}</span>
            </div>
        </div>
    @endif
</div>
