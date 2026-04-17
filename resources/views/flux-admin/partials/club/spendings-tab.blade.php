<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-x-auto">
        @if($spendings->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column></flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>POS Invoice</flux:table.column>
                    <flux:table.column>Total</flux:table.column>
                    <flux:table.column>Paid Amount</flux:table.column>
                    <flux:table.column>Unpaid</flux:table.column>
                    <flux:table.column>Is Paid?</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($spendings as $spending)
                        <flux:table.row wire:key="spending-{{ $spending->id }}">
                            <flux:table.cell>
                                @if($spending->payments->count())
                                    <button wire:click="togglePayments({{ $spending->id }})" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                                        <flux:icon :name="$expandedSpendingId === $spending->id ? 'chevron-down' : 'chevron-right'" variant="micro" class="w-4 h-4" />
                                    </button>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $spending->date ? $spending->date->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $spending->pos_invoice ?? '—' }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($spending->total, 2) }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($spending->paid_amount ?? 0, 2) }}</flux:table.cell>
                            <flux:table.cell class="font-medium {{ $spending->unpaid_amount > 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                                £{{ number_format($spending->unpaid_amount, 2) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$spending->is_paid ? 'green' : 'red'" size="sm">
                                    {{ $spending->is_paid ? 'Yes' : 'No' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $spending->user?->first_name ?? '—' }}</flux:table.cell>
                        </flux:table.row>

                        {{-- Expandable payments sub-rows --}}
                        @if($expandedSpendingId === $spending->id && $spending->payments->count())
                            @foreach($spending->payments as $payment)
                                <flux:table.row wire:key="payment-{{ $payment->id }}" class="bg-zinc-50 dark:bg-zinc-900/50">
                                    <flux:table.cell></flux:table.cell>
                                    <flux:table.cell class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $payment->date ? $payment->date->format('d M Y') : '—' }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs">{{ $payment->pos_invoice ?? '—' }}</flux:table.cell>
                                    <flux:table.cell colspan="2" class="text-xs font-medium text-green-600 dark:text-green-400">
                                        + £{{ number_format($payment->received_total, 2) }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs max-w-xs truncate">{{ $payment->note ?? '—' }}</flux:table.cell>
                                    <flux:table.cell></flux:table.cell>
                                    <flux:table.cell class="text-xs">{{ $payment->user?->first_name ?? '—' }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        @endif
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No spendings found.
            </div>
        @endif
    </div>
</div>
