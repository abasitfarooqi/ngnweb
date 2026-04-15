<div>
    @if($charges->isNotEmpty())
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Paid?</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($charges as $charge)
                        <flux:table.row wire:key="charge-{{ $charge->id }}">
                            <flux:table.cell class="font-medium">#{{ $charge->id }}</flux:table.cell>
                            <flux:table.cell>{{ $charge->description ?? '—' }}</flux:table.cell>
                            <flux:table.cell>£{{ $charge->getRawOriginal('amount') ? number_format($charge->getRawOriginal('amount'), 2) : '0.00' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$charge->getRawOriginal('is_paid') ? 'green' : 'red'" size="sm">
                                    {{ $charge->getRawOriginal('is_paid') ? 'Paid' : 'Unpaid' }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @else
        <div class="p-8 text-center">
            <flux:icon name="receipt-percent" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No other charges found for this booking.</p>
        </div>
    @endif
</div>
