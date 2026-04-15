<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        @if($purchases->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>POS Invoice</flux:table.column>
                    <flux:table.column>Total</flux:table.column>
                    <flux:table.column>Discount %</flux:table.column>
                    <flux:table.column>Discount Amount</flux:table.column>
                    <flux:table.column>Redeemed?</flux:table.column>
                    <flux:table.column>Redeem Amount</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($purchases as $purchase)
                        <flux:table.row wire:key="purchase-{{ $purchase->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $purchase->date ? \Carbon\Carbon::parse($purchase->date)->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $purchase->pos_invoice ?? '—' }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($purchase->total, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $purchase->percent }}%</flux:table.cell>
                            <flux:table.cell>£{{ number_format($purchase->discount, 2) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$purchase->is_redeemed ? 'green' : 'zinc'" size="sm">
                                    {{ $purchase->is_redeemed ? 'Yes' : 'No' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>£{{ number_format($purchase->redeem_amount ?? 0, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $purchase->user?->first_name ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No purchases found.
            </div>
        @endif
    </div>
</div>
