<div>
    {{-- Pay Invoice Modal --}}
    <flux:modal name="pay-invoice-modal" class="w-full max-w-md">
        <div class="p-5">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-4">Mark Invoice as Paid</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select wire:model="paymentMethodId" class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red">
                        <option value="">— Select Method —</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->title }}</option>
                        @endforeach
                    </select>
                    @error('paymentMethodId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Amount Received (£) <span class="text-red-500">*</span></label>
                    <input
                        wire:model="paymentAmount"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    />
                    @error('paymentAmount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <flux:button variant="ghost" x-on:click="$flux.close('pay-invoice-modal')">Cancel</flux:button>
                <button
                    wire:click="markPaid"
                    class="px-4 py-2 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                >Confirm Payment</button>
            </div>
        </div>
    </flux:modal>

    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    {{-- Summary bar --}}
    @if($totalUnpaid > 0)
        <div class="mx-4 mt-4 flex items-center gap-2 p-2 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700">
            <flux:icon name="exclamation-triangle" variant="outline" class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" />
            <p class="text-xs font-semibold text-red-700 dark:text-red-300">Total unpaid: <strong>£{{ number_format($totalUnpaid, 2) }}</strong></p>
        </div>
    @endif

    <div class="touch-pan-x overflow-x-auto mt-3">
        <div class="min-w-[60rem] md:min-w-0">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Invoice Date</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Deposit</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Paid Date</flux:table.column>
                    <flux:table.column>WhatsApp</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>&nbsp;</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($invoices as $invoice)
                        <flux:table.row wire:key="invoice-{{ $invoice->id }}" class="{{ !$invoice->is_paid ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                            <flux:table.cell class="font-medium">#{{ $invoice->id }}</flux:table.cell>
                            <flux:table.cell>
                                <span class="text-sm font-semibold">{{ $invoice->invoice_date?->format('d M Y') ?? '—' }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold">£{{ number_format($invoice->amount, 2) }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($invoice->deposit, 2) }}</flux:table.cell>
                            <flux:table.cell>
                                @if($invoice->is_paid)
                                    <flux:badge color="emerald" size="sm">Paid</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Unpaid</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $invoice->paid_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @if($invoice->is_whatsapp_sent)
                                    <flux:badge color="emerald" size="sm" icon="check">Sent</flux:badge>
                                    @if($invoice->whatsapp_last_reminder_sent_at)
                                        <span class="block text-xs text-zinc-400">{{ $invoice->whatsapp_last_reminder_sent_at->format('d M H:i') }}</span>
                                    @endif
                                @else
                                    @if(!$invoice->is_paid)
                                        <button
                                            wire:click="markWhatsAppSent({{ $invoice->id }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                                        >
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.531 5.853L0 24l6.337-1.662A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.934 0-3.742-.521-5.291-1.428l-.379-.224-3.932 1.03 1.048-3.83-.247-.393A9.95 9.95 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                            Remind
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-400">—</span>
                                    @endif
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $invoice->user?->first_name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @if(!$invoice->is_paid)
                                    <button
                                        wire:click="openPayModal({{ $invoice->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-brand-red hover:opacity-90 text-white transition"
                                    >Pay</button>
                                @else
                                    <button
                                        wire:click="reversePayment({{ $invoice->id }})"
                                        wire:confirm="Reverse the latest payment on this invoice?"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition"
                                    >Reverse</button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                No invoices found for this booking.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
