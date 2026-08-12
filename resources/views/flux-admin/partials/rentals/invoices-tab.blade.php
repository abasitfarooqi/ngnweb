<div>
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <livewire:flux-admin.partials.rentals.schedule-tab :booking-id="$bookingId" :key="'schedule-invoices-' . $bookingId" />
    </div>

    <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
        <livewire:flux-admin.partials.rentals.rental-price-editor :bookingId="$bookingId" :key="'price-invoices-' . $bookingId" />
    </div>

    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    <div class="px-4 pt-4 pb-2 border-b border-zinc-200 dark:border-zinc-700">
        <h3 class="text-sm font-bold uppercase tracking-wide text-zinc-800 dark:text-zinc-200">Payment history</h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Click an unpaid row to expand details and send WhatsApp reminders.</p>
    </div>

    @if($totalUnpaid > 0)
        <div class="mx-4 mt-4 flex items-center gap-2 p-2 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700">
            <flux:icon name="exclamation-triangle" variant="outline" class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" />
            <p class="text-xs font-semibold text-red-700 dark:text-red-300">Total outstanding: <strong>£{{ number_format($totalUnpaid, 2) }}</strong></p>
        </div>
    @endif

    <div class="touch-pan-x overflow-x-auto mt-3">
        <div class="min-w-[72rem] md:min-w-0">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Invoice ID</flux:table.column>
                    <flux:table.column>Tran. no</flux:table.column>
                    <flux:table.column>Invoice date</flux:table.column>
                    <flux:table.column>Invoice amount</flux:table.column>
                    <flux:table.column>Paid amount</flux:table.column>
                    <flux:table.column>Paid date</flux:table.column>
                    <flux:table.column>Invoice state</flux:table.column>
                    <flux:table.column>Deposit</flux:table.column>
                    <flux:table.column>Received by</flux:table.column>
                    <flux:table.column>Posting time</flux:table.column>
                    <flux:table.column>Action</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($invoices as $invoice)
                        @php
                            $isPaid = (bool) $invoice->is_paid;
                            $outstanding = max((float) $invoice->outstanding_balance, 0);
                            $rowClass = $isPaid ? '' : 'bg-red-50 dark:bg-red-900/10 cursor-pointer';
                        @endphp
                        <flux:table.row
                            wire:key="invoice-row-{{ $invoice->id }}"
                            class="{{ $rowClass }}"
                            wire:click="toggleInvoice({{ $invoice->id }})"
                        >
                            <flux:table.cell class="font-medium text-xs">#{{ $invoice->id }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $invoice->transaction_no ?: '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs font-semibold">
                                {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-xs font-semibold">£{{ number_format((float) $invoice->amount, 2) }}</flux:table.cell>
                            <flux:table.cell class="text-xs">£{{ number_format((float) $invoice->total_paid_amount, 2) }}</flux:table.cell>
                            <flux:table.cell class="text-xs">
                                {{ $invoice->paid_date ? \Carbon\Carbon::parse($invoice->paid_date)->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $invoice->state ?: '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs">£{{ number_format((float) $invoice->deposit, 2) }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $invoice->received_by ?: '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs">
                                {{ $invoice->transaction_datetime ? \Carbon\Carbon::parse($invoice->transaction_datetime)->format('d M Y H:i') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($isPaid)
                                    <div class="flex items-center gap-1">
                                        <flux:badge color="emerald" size="sm">Paid</flux:badge>
                                        <button
                                            type="button"
                                            wire:click.stop="reversePayment({{ $invoice->id }})"
                                            wire:confirm="Mark this invoice as unpaid again? This reverses the latest payment and sends an unpaid warning email to the customer."
                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold border border-amber-400 text-amber-800 hover:bg-amber-50 dark:border-amber-600 dark:text-amber-300 dark:hover:bg-amber-900/20 transition"
                                        >Reverse</button>
                                    </div>
                                @else
                                    <button
                                        type="button"
                                        wire:click.stop="openPayModal({{ $invoice->id }}, {{ $outstanding }})"
                                        class="inline-flex items-center px-2 py-1 text-xs font-bold bg-red-600 hover:bg-red-700 text-white transition"
                                    >UnPaid</button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>

                        @if(!$isPaid && $expandedInvoiceId === $invoice->id)
                            <flux:table.row wire:key="invoice-detail-{{ $invoice->id }}" class="bg-zinc-50 dark:bg-zinc-800/50">
                                <flux:table.cell colspan="11" class="!p-4">
                                    <div class="space-y-4" wire:click.stop>
                                        @if(empty($expandedDetail))
                                            <p class="text-sm text-red-600">Could not load invoice details.</p>
                                        @else
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Invoice details &amp; reminder management</h4>

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Customer information</p>
                                                <dl class="space-y-1 text-sm">
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Name</dt><dd>{{ $expandedDetail['customer_name'] ?: 'N/A' }}</dd></div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Phone</dt><dd>{{ $expandedDetail['customer_phone'] ?: 'N/A' }}</dd></div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">WhatsApp</dt><dd>{{ $expandedDetail['customer_whatsapp'] ?: ($expandedDetail['customer_phone'] ?: 'N/A') }}</dd></div>
                                                </dl>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Motorbike information</p>
                                                <dl class="space-y-1 text-sm">
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Registration</dt><dd>{{ $expandedDetail['motorbike_reg_no'] ?: 'N/A' }}</dd></div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Weekly rent</dt><dd>£{{ number_format((float) $expandedDetail['weekly_rent'], 2) }}</dd></div>
                                                </dl>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Invoice details</p>
                                                <dl class="space-y-2 text-sm">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <dt class="text-zinc-500 min-w-[7rem]">Invoice date</dt>
                                                        <dd>
                                                            <input
                                                                type="date"
                                                                value="{{ $expandedDetail['invoice_date'] ? \Carbon\Carbon::parse($expandedDetail['invoice_date'])->format('Y-m-d') : '' }}"
                                                                wire:change="updateInvoiceDate({{ $invoice->id }}, $event.target.value)"
                                                                class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-2 py-1 text-sm"
                                                            />
                                                        </dd>
                                                    </div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Amount</dt><dd>£{{ number_format((float) $expandedDetail['amount'], 2) }}</dd></div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Outstanding</dt><dd>£{{ number_format($outstanding, 2) }}</dd></div>
                                                    <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Status</dt><dd><flux:badge color="red" size="sm">Unpaid</flux:badge></dd></div>
                                                </dl>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">WhatsApp reminder</p>
                                                <dl class="space-y-1 text-sm mb-3">
                                                    <div class="flex gap-2">
                                                        <dt class="text-zinc-500 min-w-[7rem]">Reminder sent</dt>
                                                        <dd>
                                                            @if($expandedDetail['is_whatsapp_sent'])
                                                                <flux:badge color="emerald" size="sm">Yes</flux:badge>
                                                            @else
                                                                <flux:badge color="amber" size="sm">No</flux:badge>
                                                            @endif
                                                        </dd>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <dt class="text-zinc-500 min-w-[7rem]">Last reminder</dt>
                                                        <dd>
                                                            {{ ! empty($expandedDetail['whatsapp_last_reminder_sent_at']) ? \Carbon\Carbon::parse($expandedDetail['whatsapp_last_reminder_sent_at'])->format('d M Y H:i') : 'N/A' }}
                                                        </dd>
                                                    </div>
                                                </dl>
                                                <button
                                                    type="button"
                                                    wire:click="sendWhatsAppReminder({{ $invoice->id }})"
                                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                                                >
                                                    Send WhatsApp reminder
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endif
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="11" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                No posted invoices found for this booking.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    <flux:modal wire:model.self="showPayModal" class="w-full max-w-md">
        <div class="p-5">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-1">Receive payment</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">
                £{{ number_format($paymentOutstanding, 2) }} is the total amount payable on this invoice.
            </p>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment method <span class="text-red-500">*</span></label>
                    <select wire:model="paymentMethodId" class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red">
                        <option value="">— Select method —</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->title }}</option>
                        @endforeach
                    </select>
                    @error('paymentMethodId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Amount received (£) <span class="text-red-500">*</span></label>
                    <input
                        wire:model="paymentAmount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    />
                    @error('paymentAmount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <flux:button type="button" variant="ghost" wire:click="closePayModal">Cancel</flux:button>
                <flux:button type="button" variant="primary" wire:click="markPaid">Confirm payment</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
