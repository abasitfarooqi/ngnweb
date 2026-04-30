<div class="space-y-6">
    <div>
        <flux:heading size="xl">Rental due payments</flux:heading>
        <flux:text class="mt-1">Open invoices dated today or earlier on active rentals. Includes ready-to-send WhatsApp reminders.</flux:text>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Weekly</flux:table.column>
                <flux:table.column>Invoice date</flux:table.column>
                <flux:table.column>Last reminder</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($records as $r)
                    <flux:table.row wire:key="du-{{ $r->invoice_id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white font-mono text-sm">#{{ $r->booking_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $r->weekly, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-red-600 dark:text-red-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->invoice_date)->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            @if($r->is_whatsapp_sent)
                                <span class="text-emerald-600 dark:text-emerald-400">Sent</span>
                                @if($r->whatsapp_last_reminder_sent_at)
                                    · {{ \Carbon\Carbon::parse($r->whatsapp_last_reminder_sent_at)->format('d M H:i') }}
                                @endif
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" icon="chat-bubble-left-ellipsis" class="!rounded-none" href="{{ $r->whatsapp_url }}" target="_blank">WhatsApp</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="markInvoiceReminderSent({{ $r->invoice_id }})" icon="check" class="!rounded-none">Mark sent</flux:button>
                                <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none" href="{{ route('flux-admin.rentals.show', $r->booking_no) }}" wire:navigate>View</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No overdue invoices.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
