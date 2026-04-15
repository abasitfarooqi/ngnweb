<div>
    <div class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Method</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($transactions as $txn)
                    <flux:table.row wire:key="txn-{{ $txn->id }}">
                        <flux:table.cell>{{ \Carbon\Carbon::parse($txn->transaction_date)->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $txn->transactionType?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $txn->paymentMethod?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="font-medium">£{{ number_format($txn->amount, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ $txn->invoice_id ? '#' . $txn->invoice_id : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $txn->user?->first_name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="max-w-xs truncate">{{ $txn->notes ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No transactions found for this booking.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
