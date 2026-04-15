<div>
    <div class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Invoice Date</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Deposit</flux:table.column>
                <flux:table.column>Paid?</flux:table.column>
                <flux:table.column>Paid Date</flux:table.column>
                <flux:table.column>State</flux:table.column>
                <flux:table.column>User</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($invoices as $invoice)
                    <flux:table.row wire:key="invoice-{{ $invoice->id }}">
                        <flux:table.cell class="font-medium">#{{ $invoice->id }}</flux:table.cell>
                        <flux:table.cell>{{ $invoice->invoice_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($invoice->amount, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($invoice->deposit, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($invoice->is_paid)
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Unpaid</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $invoice->paid_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="zinc" size="sm">{{ ucfirst($invoice->state ?? 'N/A') }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $invoice->user?->first_name ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No invoices found for this booking.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
