<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Received</flux:table.column>
                <flux:table.column>Outstanding</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($payments as $row)
                    <flux:table.row wire:key="payment-{{ $row->id }}">
                        <flux:table.cell>{{ $row->payment_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->payment_type ?? '—' }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($row->received ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($row->outstanding ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell class="max-w-[200px] truncate">{{ $row->notes ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No payments found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
