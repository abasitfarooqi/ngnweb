@php
    $yesNo = fn (bool $value) => $value ? 'Yes' : 'No';
    $referrer = $referral->referrer;
    $referred = $referral->referred;
@endphp

<div class="space-y-6">
    <x-flux-admin::summary-header
        :title="'Referral #'.$referral->id"
        :subtitle="$referral->referral_code"
        :backUrl="route('flux-admin.rental-referrals.index')"
        backLabel="Back to referrals"
        :badges="[
            ['label' => ucfirst($referral->status), 'color' => $referral->status === 'approved' ? 'emerald' : ($referral->status === 'review' ? 'amber' : 'zinc')],
        ]"
    />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Referrer</h2>
            @if($referrer)
                <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">ID</dt><dd>{{ $referrer->id }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Name</dt><dd>{{ $referrer->first_name }} {{ $referrer->last_name }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Phone</dt><dd>{{ $referrer->phone }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Email</dt><dd>{{ $referrer->email }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Pending points</dt><dd>{{ $pendingPoints }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Available points</dt><dd>{{ $availablePoints }}</dd></div>
                </dl>
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                    <a href="{{ route('flux-admin.customers.show', $referrer->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Open customer</a>
                    @if($referral->referrer_qualifying_booking_id)
                        <a href="{{ route('flux-admin.rentals.show', $referral->referrer_qualifying_booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Rental that made them eligible to refer #{{ $referral->referrer_qualifying_booking_id }}</a>
                    @endif
                    @if($referrerActiveBooking && (int) $referrerActiveBooking->id !== (int) $referral->referrer_qualifying_booking_id)
                        <a href="{{ route('flux-admin.rentals.show', $referrerActiveBooking) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Rental active when they referred #{{ $referrerActiveBooking->id }}</a>
                    @endif
                </div>
                <ul class="mt-3 text-xs text-zinc-600 dark:text-zinc-400 space-y-1">
                    @forelse($referrerHistory as $booking)
                        <li>
                            <a href="{{ route('flux-admin.rentals.show', $booking) }}" class="hover:underline">Booking #{{ $booking->id }}</a>
                            · {{ $booking->is_posted ? 'posted' : 'intake' }}
                            · {{ optional($booking->start_date)->format('d M Y') ?: 'no start' }}
                        </li>
                    @empty
                        <li>No bookings.</li>
                    @endforelse
                </ul>
            @endif
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Referred</h2>
            <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div><dt class="text-xs text-zinc-500">Submitted name</dt><dd>{{ $referral->submitted_name }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Submitted phone</dt><dd>{{ $referral->submitted_phone }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Submitted email</dt><dd>{{ $referral->submitted_email ?: '—' }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Created</dt><dd>{{ $referral->created_at?->format('d M Y H:i') }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Matched customer</dt><dd>{{ $referred ? '#'.$referred->id.' '.$referred->first_name.' '.$referred->last_name : '—' }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Qualifying invoice</dt>
                    <dd>
                        @if($referral->referred_qualifying_invoice_id)
                            #{{ $referral->referred_qualifying_invoice_id }}
                            · {{ optional($referral->referredQualifyingInvoice?->paid_date)->format('d M Y') }}
                            · £{{ number_format((float) ($referral->referredQualifyingInvoice?->amount ?? 0), 2) }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
            @if($referred)
                <a href="{{ route('flux-admin.customers.show', $referred->id) }}" class="mt-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Open customer</a>
            @endif
            @if($referral->referred_qualifying_booking_id)
                <a href="{{ route('flux-admin.rentals.show', $referral->referred_qualifying_booking_id) }}" class="mt-3 ml-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Open qualifying booking</a>
            @endif
            <ul class="mt-3 text-xs text-zinc-600 dark:text-zinc-400 space-y-1">
                @forelse($referredHistory as $booking)
                    <li>
                        <a href="{{ route('flux-admin.rentals.show', $booking) }}" class="hover:underline">Booking #{{ $booking->id }}</a>
                        · {{ $booking->is_posted ? 'posted' : 'intake' }}
                    </li>
                @empty
                    <li>No bookings.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Checks</h2>
        @if($referral->status === 'review')
            @if($readyToApprove)
                <p class="mt-3 text-sm font-medium text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-2">System is green. This referral is good to approve.</p>
            @else
                <p class="mt-3 text-sm font-medium text-red-800 dark:text-red-200 border border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-3 py-2">Red checks still need to be clear before approval.</p>
            @endif
        @endif
        @if($referral->status === 'approved' && $credit && $credit->status !== 'redeemed')
            <p class="mt-3 text-sm border border-zinc-200 dark:border-zinc-700 px-3 py-2">
                Approved. This free week can be used now, once, on any unpaid weekly invoice of the referrer’s posted rentals.
            </p>
        @endif
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
            @foreach($checks as $key => $value)
                @php
                    $healthy = is_bool($value) ? $checkIsHealthy($key, $value) : true;
                    $badgeClass = $healthy
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200'
                        : 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-200';
                @endphp
                <div class="flex justify-between gap-3 border border-zinc-100 dark:border-zinc-800 px-3 py-2">
                    <span class="text-zinc-500">{{ str_replace('_', ' ', $key) }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">{{ is_bool($value) ? $yesNo($value) : ($value ?: '—') }}</span>
                </div>
            @endforeach
        </div>
        @if($checkNote)
            <p class="mt-3 text-sm text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/30 px-3 py-2">{{ $checkNote }}</p>
        @endif
        @if($referral->hasWarning())
            <pre class="mt-3 text-xs bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 p-3 overflow-x-auto">{{ json_encode($referral->warnings, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>

    @if($canReview)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 space-y-3">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Review</h2>
            <flux:textarea wire:model="reviewReason" rows="3" placeholder="Reason for approve or disapprove" class="!rounded-none" />
            @error('reviewReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            @error('review_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="flex flex-wrap gap-2 items-center">
                @if($referral->status === 'review' && $credit?->status === 'pending')
                    <flux:button size="sm" variant="primary" wire:click="approve" wire:confirm="Approve this reward?" class="!rounded-none">Approve</flux:button>
                    <flux:button size="sm" variant="danger" wire:click="reject" wire:confirm="Disapprove this referral?" class="!rounded-none">Disapprove</flux:button>
                @elseif(in_array($referral->status, ['approved', 'rejected'], true) && $credit?->status !== 'redeemed')
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">
                        {{ $referral->status === 'approved' ? 'Approved' : 'Disapproved' }}{{ $referral->reviewed_at ? ' on '.$referral->reviewed_at->format('d M Y H:i') : '' }}.
                    </p>
                    <flux:button size="sm" variant="ghost" wire:click="undoReview" wire:confirm="Undo this review and send it back to waiting?" class="!rounded-none">Undo</flux:button>
                @elseif($credit?->status === 'redeemed')
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Free week already used. Review is locked.</p>
                @endif
            </div>

            @if($credit?->status === 'redeemed')
                <p class="pt-3 border-t border-zinc-200 dark:border-zinc-800 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                    Free week already applied to invoice #{{ $credit->redeemed_invoice_id }}. This reward is locked.
                </p>
            @elseif($referral->status === 'approved' && $credit)
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
                    @if(! $credit->isSpendable())
                        <p class="text-sm">Points are being transferred and will be usable from {{ optional($credit->available_from)->format('d M Y H:i') ?: 'now' }}.</p>
                        <p class="text-sm">Early apply: pick one unpaid invoice. Explain this to the boss. Approval already covered the background story.</p>
                    @else
                        <p class="text-sm">Pick one unpaid invoice on any of the referrer’s posted rentals. Once only. No extra explanation — the boss already has the approval story.</p>
                    @endif
                    <select wire:model="redeemInvoiceId" class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">Choose invoice</option>
                        @foreach($redeemableInvoices as $invoice)
                            <option value="{{ $invoice->id }}">#{{ $invoice->id }} · booking {{ $invoice->booking_id }} · {{ optional($invoice->invoice_date)->format('d M Y') }} · £{{ number_format((float) $invoice->amount, 2) }}</option>
                        @endforeach
                    </select>
                    @error('redeemInvoiceId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    @if(! $credit->isSpendable())
                        <flux:input wire:model="releaseReason" placeholder="Explain this early apply for the boss" class="!rounded-none" />
                        @error('releaseReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('release_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <flux:button size="sm" variant="primary" wire:click="releaseEarly" wire:confirm="Apply this one-time free week now?" class="!rounded-none">Release early and apply</flux:button>
                    @else
                        <flux:button size="sm" variant="primary" wire:click="redeem" wire:confirm="Apply this one-time free week now?" class="!rounded-none">Apply free week</flux:button>
                    @endif
                    @if($redeemableInvoices->isEmpty())
                        <p class="text-xs text-zinc-500">No unpaid weekly invoices on the referrer’s bookings.</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if(! $referral->referred_customer_id)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Suggest match</h2>
            <flux:input wire:model.live.debounce.300ms="matchSearch" placeholder="Search customers…" class="!rounded-none mt-2" />
            @if($matchChoices->isNotEmpty())
                <div class="mt-2 border border-zinc-200 dark:border-zinc-700">
                    @foreach($matchChoices as $choice)
                        <button type="button" wire:click="$set('matchCustomerId', {{ $choice->id }})" class="block w-full text-left px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            #{{ $choice->id }} {{ $choice->first_name }} {{ $choice->last_name }} · {{ $choice->phone }}
                        </button>
                    @endforeach
                </div>
            @endif
            @if($matchCustomerId)
                <p class="text-xs mt-2">Selected #{{ $matchCustomerId }}</p>
                <flux:button size="sm" class="!rounded-none mt-2" wire:click="matchCustomer">Match this customer</flux:button>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Activity</h2>
            <ul class="mt-3 space-y-2 text-xs">
                @forelse($portalLogs as $log)
                    <li class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        <span class="font-medium">{{ $log->action }}</span>
                        · {{ $log->created_at?->format('d M Y H:i') }}
                        · {{ $log->changedBy?->name ?? ($log->changed_by ? 'user #'.$log->changed_by : 'system') }}
                    </li>
                @empty
                    <li class="text-zinc-500">No activity yet.</li>
                @endforelse
            </ul>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Internal notes</h2>
            <flux:textarea wire:model="note" rows="3" placeholder="Staff note (not shown on the portal)" class="!rounded-none" />
            <flux:button size="sm" class="!rounded-none mt-2" wire:click="addNote">Add note</flux:button>
            <ul class="mt-3 space-y-2 text-xs">
                @forelse($notes as $log)
                    <li class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        {{ $log->created_at?->format('d M Y H:i') }}
                        · {{ $log->changedBy?->name ?? 'staff' }}
                        <div class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $log->new_data['note'] ?? '' }}</div>
                    </li>
                @empty
                    <li class="text-zinc-500">No notes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
