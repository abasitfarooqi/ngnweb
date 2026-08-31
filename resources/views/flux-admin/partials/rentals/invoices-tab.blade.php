<div>
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <livewire:flux-admin.partials.rentals.schedule-tab :booking-id="$bookingId" :key="'schedule-invoices-' . $bookingId" />
    </div>

    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    <div class="px-4 pt-4 pb-2 border-b border-zinc-200 dark:border-zinc-700">
        <h3 class="text-sm font-bold uppercase tracking-wide text-zinc-800 dark:text-zinc-200">Payment history</h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Click an invoice row to open details directly underneath it.</p>
    </div>

    @if($totalUnpaid > 0)
        <div class="mx-4 mt-4 flex items-center gap-2 p-2 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700">
            <flux:icon name="exclamation-triangle" variant="outline" class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" />
            <p class="text-xs font-semibold text-red-700 dark:text-red-300">Total outstanding: <strong>£{{ number_format($totalUnpaid, 2) }}</strong></p>
        </div>
    @endif

    <div class="invoice-table-scroll touch-pan-x overflow-x-auto mt-3">
        <div class="min-w-[72rem]">
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
                            $isDue = (bool) $invoice->is_due;
                            $outstanding = max((float) $invoice->outstanding_balance, 0);
                            $isExpanded = $expandedInvoiceId === $invoice->id;
                            $rowClass = $isPaid ? 'cursor-pointer' : ($isDue ? 'bg-red-50 dark:bg-red-900/10 cursor-pointer' : 'bg-amber-50/60 dark:bg-amber-900/10 cursor-pointer');
                        @endphp
                        <flux:table.row
                            wire:key="invoice-row-{{ $invoice->id }}"
                            class="{{ $rowClass }} {{ $isExpanded ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
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
                                        class="inline-flex items-center px-2 py-1 text-xs font-bold {{ $isDue ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white transition"
                                    >{{ $isDue ? 'UnPaid' : 'Future' }}</button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                        @if($isExpanded)
                            @php
                                $expandedIsPaid = $isPaid;
                                $expandedOutstanding = $outstanding;
                            @endphp
                            <flux:table.row
                                wire:key="invoice-detail-row-{{ $invoice->id }}"
                                class="invoice-accordion-row"
                            >
                                <flux:table.cell colspan="11" class="invoice-accordion-cell">
                                    <div class="invoice-detail-panel border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50" wire:key="invoice-detail-{{ $invoice->id }}">
                                        @if(empty($expandedDetail))
                                            <p class="text-sm text-red-600">Could not load invoice details.</p>
                                        @else
                                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Invoice details &amp; reminder management</h4>

                                            <div class="invoice-detail-grid">
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Customer information</p>
                                                    <dl class="invoice-kv text-sm">
                                                        <dt>Name</dt><dd>{{ $expandedDetail['customer_name'] ?: 'N/A' }}</dd>
                                                        <dt>Phone</dt><dd>{{ $expandedDetail['customer_phone'] ?: 'N/A' }}</dd>
                                                        <dt>WhatsApp</dt><dd>{{ $expandedDetail['customer_whatsapp'] ?: ($expandedDetail['customer_phone'] ?: 'N/A') }}</dd>
                                                    </dl>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Motorbike information</p>
                                                    <dl class="invoice-kv text-sm">
                                                        <dt>Registration</dt><dd>{{ $expandedDetail['motorbike_reg_no'] ?: 'N/A' }}</dd>
                                                        <dt>Weekly rent</dt><dd>£{{ number_format((float) $expandedDetail['weekly_rent'], 2) }}</dd>
                                                    </dl>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">Invoice details</p>
                                                    <dl class="invoice-kv text-sm">
                                                        <dt>Invoice date</dt>
                                                        <dd>
                                                            <input
                                                                type="date"
                                                                value="{{ $expandedDetail['invoice_date'] ? \Carbon\Carbon::parse($expandedDetail['invoice_date'])->format('Y-m-d') : '' }}"
                                                                wire:change="updateInvoiceDate({{ $invoice->id }}, $event.target.value)"
                                                                class="w-full max-w-[11rem] border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-2 py-1 text-sm"
                                                            />
                                                        </dd>
                                                        <dt>Amount</dt><dd>£{{ number_format((float) $expandedDetail['amount'], 2) }}</dd>
                                                        <dt>Outstanding</dt><dd>£{{ number_format($expandedOutstanding, 2) }}</dd>
                                                        <dt>Status</dt>
                                                        <dd>
                                                            @if($expandedIsPaid)
                                                                <flux:badge color="emerald" size="sm">Paid</flux:badge>
                                                            @else
                                                                <flux:badge color="red" size="sm">Unpaid</flux:badge>
                                                            @endif
                                                        </dd>
                                                    </dl>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500 mb-2">WhatsApp reminder</p>
                                                    <dl class="invoice-kv text-sm">
                                                        <dt>Reminder sent</dt>
                                                        <dd>
                                                            @if($expandedDetail['is_whatsapp_sent'])
                                                                <flux:badge color="emerald" size="sm">Yes</flux:badge>
                                                            @else
                                                                <flux:badge color="amber" size="sm">No</flux:badge>
                                                            @endif
                                                        </dd>
                                                        <dt>Last reminder</dt>
                                                        <dd>{{ ! empty($expandedDetail['whatsapp_last_reminder_sent_at']) ? \Carbon\Carbon::parse($expandedDetail['whatsapp_last_reminder_sent_at'])->format('d M Y H:i') : 'N/A' }}</dd>
                                                    </dl>
                                                    <button
                                                        type="button"
                                                        wire:click.stop="sendWhatsAppReminder({{ $invoice->id }})"
                                                        class="mt-3 inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                                                    >
                                                        Send WhatsApp reminder
                                                    </button>
                                                </div>
                                            </div>

                                            <livewire:flux-admin.partials.rentals.weekly-updates-panel
                                                :booking-id="$bookingId"
                                                :invoice-id="$invoice->id"
                                                :key="'invoice-weekly-updates-'.$invoice->id"
                                            />

                                            @if(! empty($expandedAward) && (int) ($expandedAward['awarded_invoice_id'] ?? 0) === (int) $invoice->id)
                                                <div class="mt-4 border border-zinc-200 dark:border-zinc-700 p-3 space-y-2">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">Free week log</p>
                                                    <p class="text-xs">
                                                        {{ ($expandedAward['source'] ?? '') === 'direct' ? 'Direct' : 'Programme referral' }}
                                                        · £{{ number_format((float) ($expandedAward['amount'] ?? 0), 2) }}
                                                        @if(! empty($expandedAward['referral_id']))
                                                            · <a href="{{ route('flux-admin.rental-referrals.show', $expandedAward['referral_id']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">referral #{{ $expandedAward['referral_id'] }}</a>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs">
                                                        Hirer #{{ $expandedAward['hirer_customer_id'] ?? '—' }}
                                                        · selected referrer #{{ $expandedAward['selected_referrer_customer_id'] ?? '—' }}
                                                        {{ trim(($expandedAward['selected_referrer']['first_name'] ?? '').' '.($expandedAward['selected_referrer']['last_name'] ?? '')) }}
                                                    </p>
                                                    @include('flux-admin.partials.rentals.referrer-paid-invoices', [
                                                        'invoices' => $expandedAward['selected_paid_invoices'] ?? [],
                                                        'missing' => empty($expandedAward['selected_paid_invoices']),
                                                        'message' => $expandedAward['eligibility_note'] ?? null,
                                                        'bookingId' => $expandedAward['selected_referrer_booking_id'] ?? null,
                                                    ])
                                                    @if(! empty($expandedAward['staff_proof']))
                                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Staff explanation: {{ $expandedAward['staff_proof'] }}</p>
                                                    @endif
                                                </div>
                                            @endif
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
                    <select wire:model.live="paymentKind" class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        @if($canApplyFreeWeek)
                            @if($programmeReferrals->isNotEmpty())
                                <option value="referral">Programme referral</option>
                            @endif
                            <option value="direct">Staff direct</option>
                        @endif
                    </select>
                    @if(! $canApplyFreeWeek && $freeWeekBlockReason)
                        <p class="text-xs text-zinc-500 mt-1">{{ $freeWeekBlockReason }}</p>
                    @endif
                    @error('paymentMethodId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @if($paymentKind === 'referral')
                    <div class="space-y-3">
                        @if($needsEarlyApply)
                            <p class="text-xs text-zinc-500">Early apply. Pick the friend they referred and explain this to the boss. Approval already covered the background story.</p>
                        @elseif($needsExtraFreeWeekProof)
                            <p class="text-xs text-amber-800 dark:text-amber-200">This customer already has {{ $hirerFreeWeekCount }} applied free week{{ $hirerFreeWeekCount === 1 ? '' : 's' }} (programme or direct). Another 100 from a different friend is allowed. You must explain why this extra week is being given.</p>
                        @else
                            <p class="text-xs text-zinc-500">Programme free week. Pick the friend they referred. No extra explanation — the boss already has the approval story.</p>
                        @endif
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Referred customer <span class="text-red-500">*</span></label>
                            <input
                                wire:model.live.debounce.300ms="referralSearch"
                                type="search"
                                placeholder="Name, mobile or referral number"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                            />
                        </div>
                        <div class="border border-zinc-200 dark:border-zinc-700 max-h-40 overflow-y-auto">
                            @forelse($matchedReferrals as $row)
                                <button
                                    type="button"
                                    wire:click="$set('referralId', {{ $row->id }})"
                                    class="block w-full text-left px-3 py-2 text-sm {{ (int) $referralId === (int) $row->id ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                                >
                                    {{ trim(($row->referred?->first_name.' '.$row->referred?->last_name) ?: $row->submitted_name) }}
                                    · {{ $row->referred?->phone ?: $row->submitted_phone }}
                                    · referral #{{ $row->id }}
                                </button>
                            @empty
                                <p class="px-3 py-2 text-xs text-zinc-500">No programme referral matches that search.</p>
                            @endforelse
                        </div>
                        @error('referralId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @if($referralId)
                            @include('flux-admin.partials.rentals.referrer-paid-invoices', [
                                'invoices' => $referrerEvidence['invoices'] ?? [],
                                'missing' => (bool) ($referrerEvidence['missing'] ?? false),
                                'message' => $referrerEvidence['message'] ?? null,
                                'bookingId' => $referrerEvidence['booking_id'] ?? null,
                            ])
                        @endif
                        @if($needsEarlyApply || $needsExtraFreeWeekProof)
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Explanation for the boss <span class="text-red-500">*</span></label>
                                <textarea
                                    wire:model="referralProof"
                                    rows="3"
                                    placeholder="{{ $needsExtraFreeWeekProof && ! $needsEarlyApply ? 'Why this extra free week is being given' : 'Why this is being applied before the wait ends' }}"
                                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                                ></textarea>
                                @error('referralProof') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                @elseif($paymentKind === 'direct')
                    <div class="space-y-3">
                        <p class="text-xs text-zinc-500">Staff direct free week. Search any customer. You must explain this to the boss. Unused programme points for the named person are marked redeemed. Thiago is emailed with the running free-week count.</p>
                        @if($needsExtraFreeWeekProof)
                            <p class="text-xs text-amber-800 dark:text-amber-200">This customer already has {{ $hirerFreeWeekCount }} applied free week{{ $hirerFreeWeekCount === 1 ? '' : 's' }}. This will be number {{ $hirerFreeWeekCount + 1 }}. Explain why.</p>
                        @endif
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Search any customer <span class="text-red-500">*</span></label>
                            <input
                                wire:model.live.debounce.300ms="referralSearch"
                                type="search"
                                placeholder="Name, mobile or email"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                            />
                        </div>
                        <div class="border border-zinc-200 dark:border-zinc-700 max-h-40 overflow-y-auto">
                            @forelse($directCustomers as $customer)
                                <button
                                    type="button"
                                    wire:click="$set('directCustomerId', {{ $customer->id }})"
                                    class="block w-full text-left px-3 py-2 text-sm {{ (int) $directCustomerId === (int) $customer->id ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                                >
                                    #{{ $customer->id }} {{ $customer->first_name }} {{ $customer->last_name }}
                                    · {{ $customer->phone ?: 'no phone' }}
                                </button>
                            @empty
                                <p class="px-3 py-2 text-xs text-zinc-500">Type at least two characters to search all customers.</p>
                            @endforelse
                        </div>
                        @if($selectedDirectCustomer)
                            <p class="text-xs">Selected #{{ $selectedDirectCustomer->id }} {{ $selectedDirectCustomer->first_name }} {{ $selectedDirectCustomer->last_name }}</p>
                            @include('flux-admin.partials.rentals.referrer-paid-invoices', [
                                'invoices' => $referrerEvidence['invoices'] ?? [],
                                'missing' => (bool) ($referrerEvidence['missing'] ?? false),
                                'message' => $referrerEvidence['message'] ?? null,
                                'bookingId' => $referrerEvidence['booking_id'] ?? null,
                            ])
                        @endif
                        @error('directCustomerId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Explanation for the boss <span class="text-red-500">*</span></label>
                            <textarea
                                wire:model="referralProof"
                                rows="3"
                                placeholder="Why this free week is being given, and who it relates to"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                            ></textarea>
                            @error('referralProof') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @else
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
                @endif
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <flux:button type="button" variant="ghost" wire:click="closePayModal">Cancel</flux:button>
                <flux:button type="button" variant="primary" wire:click="markPaid">
                    @if($paymentKind === 'referral' && ($needsEarlyApply || $needsExtraFreeWeekProof))
                        Release early and apply
                    @elseif(in_array($paymentKind, ['referral', 'direct'], true))
                        Apply free week
                    @else
                        Confirm payment
                    @endif
                </flux:button>
            </div>
        </div>
    </flux:modal>

    @if($freeWeekAwards->isNotEmpty())
        <div class="mx-4 mt-4 mb-4 border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-2 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-sm font-bold uppercase tracking-wide text-zinc-800 dark:text-zinc-200">Free week log</h3>
                <p class="text-xs text-zinc-500 mt-1">Programme referrals and staff direct free weeks on this booking.</p>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($freeWeekAwards as $award)
                    <div class="p-4 space-y-2" wire:key="free-week-award-{{ $award->id }}">
                        <p class="text-xs font-semibold">
                            {{ $award->source === \App\Models\RentingFreeWeekAward::SOURCE_DIRECT ? 'Direct' : 'Programme referral' }}
                            · invoice #{{ $award->awarded_invoice_id }}
                            · £{{ number_format((float) $award->amount, 2) }}
                            · {{ $award->created_at?->format('d M Y H:i') }}
                        </p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                            Hirer #{{ $award->hirer_customer_id }} {{ trim(($award->hirer?->first_name ?? '').' '.($award->hirer?->last_name ?? '')) }}
                            · selected referrer #{{ $award->selected_referrer_customer_id }} {{ trim(($award->selectedReferrer?->first_name ?? '').' '.($award->selectedReferrer?->last_name ?? '')) }}
                            @if($award->referral_id)
                                · <a href="{{ route('flux-admin.rental-referrals.show', $award->referral_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">referral #{{ $award->referral_id }}</a>
                            @endif
                            @if($award->appliedBy)
                                · applied by {{ $award->appliedBy->full_name ?: $award->appliedBy->email }}
                            @endif
                        </p>
                        @include('flux-admin.partials.rentals.referrer-paid-invoices', [
                            'invoices' => $award->selected_paid_invoices ?? [],
                            'missing' => empty($award->selected_paid_invoices),
                            'message' => $award->eligibility_note,
                            'bookingId' => $award->selected_referrer_booking_id,
                        ])
                        @if($award->staff_proof)
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Staff explanation: {{ $award->staff_proof }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
