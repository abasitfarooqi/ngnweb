<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        @if($redemptions->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>POS Invoice</flux:table.column>
                    <flux:table.column>Redeem Total</flux:table.column>
                    <flux:table.column>Note</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($redemptions as $redemption)
                        <flux:table.row wire:key="redemption-{{ $redemption->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $redemption->date ? \Carbon\Carbon::parse($redemption->date)->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $redemption->pos_invoice ?? '—' }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($redemption->redeem_total, 2) }}</flux:table.cell>
                            <flux:table.cell class="max-w-xs truncate">{{ $redemption->note ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $redemption->user?->first_name ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No redemptions found.
            </div>
        @endif
    </div>
</div>
