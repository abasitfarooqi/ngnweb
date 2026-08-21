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
                <a href="{{ route('flux-admin.customers.show', $referrer->id) }}" class="mt-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Open customer</a>
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
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
            @foreach($checks as $key => $value)
                <div class="flex justify-between gap-3 border border-zinc-100 dark:border-zinc-800 px-3 py-2">
                    <span class="text-zinc-500">{{ str_replace('_', ' ', $key) }}</span>
                    <span class="font-medium {{ $value ? 'text-zinc-900 dark:text-white' : 'text-zinc-500' }}">{{ is_bool($value) ? $yesNo($value) : ($value ?: '—') }}</span>
                </div>
            @endforeach
        </div>
        @if($referral->hasWarning())
            <pre class="mt-3 text-xs bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 p-3 overflow-x-auto">{{ json_encode($referral->warnings, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>

    @if($canReview)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 space-y-3">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Review</h2>
            <flux:textarea wire:model="reviewReason" rows="3" placeholder="Reason / notes for approve, reject, hold or unmatch" class="!rounded-none" />
            @error('reviewReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            @error('review_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="flex flex-wrap gap-2">
                <flux:button size="sm" variant="primary" wire:click="approve" wire:confirm="Approve this reward?" class="!rounded-none">Approve reward</flux:button>
                <flux:button size="sm" variant="danger" wire:click="reject" wire:confirm="Reject this referral?" class="!rounded-none">Reject</flux:button>
                <flux:button size="sm" variant="ghost" wire:click="hold" class="!rounded-none">Hold</flux:button>
                @if($referral->referred_customer_id && $credit?->status !== 'redeemed')
                    <flux:button size="sm" variant="ghost" wire:click="unmatch" wire:confirm="Remove the matched customer?" class="!rounded-none">Unmatch</flux:button>
                @endif
            </div>

            @if($referral->status === 'approved' && $credit)
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="text-xs text-zinc-500">Available from {{ optional($credit->available_from)->format('d M Y H:i') ?: 'now' }}</p>
                    <div class="mt-2 flex flex-wrap gap-2 items-end">
                        <flux:input wire:model="releaseReason" placeholder="Early release reason" class="!rounded-none" />
                        <flux:button size="sm" wire:click="releaseEarly" class="!rounded-none">Release early</flux:button>
                    </div>
                    @error('release_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            @if($credit?->isSpendable())
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="text-sm font-medium">Apply free week to an unpaid referrer invoice</p>
                    <div class="mt-2 flex flex-wrap gap-2 items-end">
                        <select wire:model="redeemInvoiceId" class="border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Choose invoice</option>
                            @foreach($redeemableInvoices as $invoice)
                                <option value="{{ $invoice->id }}">#{{ $invoice->id }} · booking {{ $invoice->booking_id }} · {{ optional($invoice->invoice_date)->format('d M Y') }} · £{{ number_format((float) $invoice->amount, 2) }}</option>
                            @endforeach
                        </select>
                        <flux:button size="sm" variant="primary" wire:click="redeem" wire:confirm="Apply the free week to this invoice?" class="!rounded-none">Apply reward</flux:button>
                    </div>
                    @error('redeemInvoiceId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($redeemableInvoices->isEmpty())
                        <p class="text-xs text-zinc-500 mt-2">No unpaid weekly invoices on the referrer’s bookings.</p>
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
