<div>
    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    {{-- Add charge form --}}
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
        <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400 mb-3">Additional Charges</h4>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3 italic">Any additional amount on top of rent (e.g. damages). PCN must be updated on /ngn-admin/.</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input
                    wire:model="description"
                    type="text"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    placeholder="Description of additional charge"
                />
                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:w-40">
                <input
                    wire:model="amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    placeholder="Amount (£)"
                />
                @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <button
                    wire:click="addCharge"
                    class="px-4 py-2 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition whitespace-nowrap"
                >Add Charge</button>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    @if($charges->isNotEmpty())
        <div class="flex flex-wrap gap-4 px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
            <div>
                <span class="text-xs text-zinc-500">Total Charges</span>
                <p class="text-sm font-bold text-zinc-900 dark:text-white">£{{ number_format($totalAmount, 2) }}</p>
            </div>
            <div>
                <span class="text-xs text-zinc-500">Paid</span>
                <p class="text-sm font-bold {{ $paidAmount >= $totalAmount ? 'text-emerald-600' : 'text-amber-600' }}">£{{ number_format($paidAmount, 2) }}</p>
            </div>
            <div>
                <span class="text-xs text-zinc-500">Outstanding</span>
                <p class="text-sm font-bold {{ ($totalAmount - $paidAmount) > 0 ? 'text-red-600' : 'text-emerald-600' }}">£{{ number_format($totalAmount - $paidAmount, 2) }}</p>
            </div>
        </div>

        <div class="px-4 pt-2 pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Click an unpaid charge to expand details and send WhatsApp or email reminders.</p>
        </div>

        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[48rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>ID</flux:table.column>
                        <flux:table.column>Description</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($charges as $charge)
                            @php
                                $isPaid = (bool) $charge->getRawOriginal('is_paid');
                                $amount = (float) str_replace(',', '', $charge->getRawOriginal('amount'));
                            @endphp
                            <flux:table.row
                                wire:key="charge-{{ $charge->id }}"
                                class="{{ $isPaid ? '' : 'bg-amber-50 dark:bg-amber-900/10 cursor-pointer' }}"
                                wire:click="toggleCharge({{ $charge->id }})"
                            >
                                <flux:table.cell class="font-medium">#{{ $charge->id }}</flux:table.cell>
                                <flux:table.cell>{{ $charge->description ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="font-semibold">£{{ number_format($amount, 2) }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$isPaid ? 'emerald' : 'amber'" size="sm">
                                        {{ $isPaid ? 'Paid' : 'Unpaid' }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if(!$isPaid)
                                        <button
                                            type="button"
                                            wire:click.stop="openPayModal({{ $charge->id }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-brand-red hover:opacity-90 text-white transition"
                                        >Pay</button>
                                    @else
                                        <span class="text-xs text-zinc-400">Settled</span>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>

                            @if(!$isPaid && $expandedChargeId === $charge->id)
                                <flux:table.row wire:key="charge-detail-{{ $charge->id }}" class="bg-zinc-50 dark:bg-zinc-800/50">
                                    <flux:table.cell colspan="5" class="!p-4">
                                        <div class="space-y-4" wire:click.stop>
                                            @if(empty($expandedDetail))
                                                <p class="text-sm text-red-600">Could not load charge details.</p>
                                            @else
                                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Charge details &amp; reminder management</h4>

                                                <div class="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Customer information</p>
                                                        <dl class="space-y-1 text-sm">
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Name</dt><dd>{{ $expandedDetail['customer_name'] ?: 'N/A' }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Phone</dt><dd>{{ $expandedDetail['customer_phone'] ?: 'N/A' }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Email</dt><dd>{{ $expandedDetail['customer_email'] ?: 'N/A' }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">WhatsApp</dt><dd>{{ $expandedDetail['customer_whatsapp'] ?: ($expandedDetail['customer_phone'] ?: 'N/A') }}</dd></div>
                                                        </dl>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Motorbike information</p>
                                                        <dl class="space-y-1 text-sm">
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Registration</dt><dd>{{ $expandedDetail['motorbike_reg_no'] ?: 'N/A' }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Booking</dt><dd>#{{ $expandedDetail['booking_id'] }}</dd></div>
                                                        </dl>
                                                    </div>
                                                </div>

                                                <div class="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Charge details</p>
                                                        <dl class="space-y-1 text-sm">
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Description</dt><dd>{{ $expandedDetail['description'] ?: '—' }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Amount</dt><dd>£{{ number_format((float) $expandedDetail['amount'], 2) }}</dd></div>
                                                            <div class="flex gap-2"><dt class="text-zinc-500 min-w-[7rem]">Status</dt><dd><flux:badge color="amber" size="sm">Unpaid</flux:badge></dd></div>
                                                        </dl>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Reminders</p>
                                                        <dl class="space-y-1 text-sm mb-3">
                                                            <div class="flex gap-2">
                                                                <dt class="text-zinc-500 min-w-[7rem]">WhatsApp sent</dt>
                                                                <dd>
                                                                    @if($expandedDetail['is_whatsapp_sent'])
                                                                        <flux:badge color="emerald" size="sm">Yes</flux:badge>
                                                                    @else
                                                                        <flux:badge color="amber" size="sm">No</flux:badge>
                                                                    @endif
                                                                </dd>
                                                            </div>
                                                            <div class="flex gap-2">
                                                                <dt class="text-zinc-500 min-w-[7rem]">Last WhatsApp</dt>
                                                                <dd>{{ ! empty($expandedDetail['whatsapp_last_reminder_sent_at']) ? \Carbon\Carbon::parse($expandedDetail['whatsapp_last_reminder_sent_at'])->format('d M Y H:i') : 'N/A' }}</dd>
                                                            </div>
                                                            <div class="flex gap-2">
                                                                <dt class="text-zinc-500 min-w-[7rem]">Last email</dt>
                                                                <dd>{{ ! empty($expandedDetail['email_last_reminder_sent_at']) ? \Carbon\Carbon::parse($expandedDetail['email_last_reminder_sent_at'])->format('d M Y H:i') : 'N/A' }}</dd>
                                                            </div>
                                                        </dl>
                                                        <div class="flex flex-wrap gap-2">
                                                            <button
                                                                type="button"
                                                                wire:click="sendWhatsAppReminder({{ $charge->id }})"
                                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                                                            >
                                                                Send WhatsApp reminder
                                                            </button>
                                                            <button
                                                                type="button"
                                                                wire:click="sendEmailReminder({{ $charge->id }})"
                                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-zinc-800 hover:bg-zinc-900 text-white transition"
                                                            >
                                                                Send email reminder
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endif
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @else
        <div class="p-8 text-center">
            <flux:icon name="receipt-percent" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No additional charges for this booking.</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Use the form above to add charges such as damages.</p>
        </div>
    @endif

    <flux:modal wire:model.self="showPayModal" class="w-full max-w-md">
        <div class="p-5">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-4">Pay additional charge</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment method</label>
                    <select wire:model="paymentMethodId" class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm">
                        <option value="">Select payment method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->title }}</option>
                        @endforeach
                    </select>
                    @error('paymentMethodId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <flux:button type="button" variant="ghost" wire:click="closePayModal">Cancel</flux:button>
                <flux:button type="button" variant="primary" wire:click="payCharge" wire:loading.attr="disabled" wire:target="payCharge">Confirm payment</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
