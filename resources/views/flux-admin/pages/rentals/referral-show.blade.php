@php
    $yesNo = fn (bool $value) => $value ? 'Yes' : 'No';
    $referrer = $referral->referrer;
    $referred = $referral->referred;
    $directCount = $directAwards->count();
    $thisFriendAwards = $programmeAwards;
    $shownAwardIds = $thisFriendAwards->pluck('id');
    $otherDirectAwards = $directAwards->reject(fn ($award) => $shownAwardIds->contains($award->id))->values();
    $statusColor = $referral->staffStatusTone() === 'green' ? 'emerald' : ($referral->staffStatusTone() === 'red' ? 'red' : 'amber');
    $pointsColor = $referral->pointsStatusTone() === 'green' ? 'emerald' : ($referral->pointsStatusTone() === 'red' ? 'red' : 'amber');
    $headerBadges = [
        ['label' => 'Programme — this friend', 'color' => 'blue'],
        ['label' => $referral->staffStatusLabel(), 'color' => $statusColor],
        ['label' => $referral->pointsStatusLabel(), 'color' => $pointsColor],
    ];
    if ($directCount > 0) {
        $headerBadges[] = ['label' => $directCount.' staff direct gift'.($directCount === 1 ? '' : 's').' — not these points', 'color' => 'zinc'];
    }
@endphp

<div class="space-y-6">
    <x-flux-admin::summary-header
        :title="'Referral #'.$referral->id"
        :subtitle="$referral->referral_code"
        :backUrl="route('flux-admin.rental-referrals.index')"
        backLabel="Back to referrals"
        :badges="$headerBadges"
    >
        <x-slot:stats>
            <div>
                <p class="text-xs text-zinc-500">This friend</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $referral->pointsStatusLabel() }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">All friends unused</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $availablePoints }} pts</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">All friends pending</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $pendingPoints }} pts</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">Staff direct gifts</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $directCount }}</p>
            </div>
        </x-slot:stats>
    </x-flux-admin::summary-header>

    @if($directCount > 0 && $credit?->status !== 'redeemed')
        <p class="text-sm border border-sky-300 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/30 text-sky-900 dark:text-sky-100 px-3 py-2">
            A staff <strong>direct</strong> free week is listed below. That gift does not spend programme points.
            This friend still has <strong>{{ $referral->pointsStatusLabel() }}</strong>.
            Apply programme separately on the referrer’s current unpaid week if that is what you intend.
        </p>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <section class="overflow-hidden border border-sky-200 dark:border-sky-900 bg-white dark:bg-zinc-900">
            <div class="flex items-start justify-between gap-3 px-5 py-4 bg-sky-700 text-white">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-100">Who sent the friend</p>
                    <h2 class="mt-1 text-lg font-bold">{{ $referrer ? trim($referrer->first_name.' '.$referrer->last_name) : 'Referrer' }}</h2>
                </div>
                <div class="p-2 bg-sky-600">
                    <flux:icon name="user" variant="outline" class="w-5 h-5" />
                </div>
            </div>
            @if($referrer)
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-flux-admin::detail-fact icon="hashtag" label="Customer ID" tone="blue">{{ $referrer->id }}</x-flux-admin::detail-fact>
                        <x-flux-admin::detail-fact icon="phone" label="Phone" tone="green">{{ $referrer->phone }}</x-flux-admin::detail-fact>
                        <x-flux-admin::detail-fact icon="envelope" label="Email" tone="indigo" class="sm:col-span-2">{{ $referrer->email ?: '—' }}</x-flux-admin::detail-fact>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">This friend</p>
                            <div class="mt-2">@include('flux-admin.partials.rentals.status-pill', ['label' => $referral->pointsStatusLabel(), 'tone' => $referral->pointsStatusTone()])</div>
                        </div>
                        <div class="border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/30 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-700 dark:text-sky-300">All friends unused / pending</p>
                            <p class="mt-2 text-lg font-bold text-zinc-900 dark:text-white">{{ $availablePoints }} <span class="text-zinc-400 font-medium">/</span> {{ $pendingPoints }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('flux-admin.customers.show', $referrer->id) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium bg-sky-700 text-white hover:bg-sky-800">Open customer</a>
                        @if($referral->referrer_qualifying_booking_id)
                            <a href="{{ route('flux-admin.rentals.show', $referral->referrer_qualifying_booking_id) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium border border-sky-300 dark:border-sky-800 text-sky-800 dark:text-sky-200 hover:bg-sky-50 dark:hover:bg-sky-950/40">Eligible on rental #{{ $referral->referrer_qualifying_booking_id }}</a>
                        @endif
                        @if($referrerActiveBooking && (int) $referrerActiveBooking->id !== (int) $referral->referrer_qualifying_booking_id)
                            <a href="{{ route('flux-admin.rentals.show', $referrerActiveBooking) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800">Active when they referred #{{ $referrerActiveBooking->id }}</a>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 mb-2">Hire history</p>
                        <ul class="border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse($referrerHistory as $booking)
                                <li class="flex items-center justify-between gap-3 px-3 py-2 text-sm {{ $loop->even ? 'bg-zinc-50 dark:bg-zinc-950/40' : 'bg-white dark:bg-zinc-900' }}">
                                    <a href="{{ route('flux-admin.rentals.show', $booking) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Booking #{{ $booking->id }}</a>
                                    <span class="text-xs text-zinc-500">{{ $booking->is_posted ? 'Posted' : 'Intake' }} · {{ optional($booking->start_date)->format('d M Y') ?: 'no start' }}</span>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-sm text-zinc-500">No bookings.</li>
                            @endforelse
                        </ul>
                    </div>
                    @if($otherProgrammeReferrals->isNotEmpty())
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 mb-2">Other friends they referred</p>
                            <ul class="space-y-2">
                                @foreach($otherProgrammeReferrals as $other)
                                    <li class="flex flex-wrap items-center gap-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/40 px-3 py-2">
                                        <a href="{{ route('flux-admin.rental-referrals.show', $other) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">#{{ $other->id }} {{ $other->submitted_name }}</a>
                                        @include('flux-admin.partials.rentals.status-pill', ['label' => $other->staffStatusLabel(), 'tone' => $other->staffStatusTone()])
                                        @include('flux-admin.partials.rentals.status-pill', ['label' => $other->pointsStatusLabel(), 'tone' => $other->pointsStatusTone()])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </section>

        <section class="overflow-hidden border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-zinc-900">
            <div class="flex items-start justify-between gap-3 px-5 py-4 bg-emerald-700 text-white">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-100">Friend they sent</p>
                    <h2 class="mt-1 text-lg font-bold">{{ $referred ? trim($referred->first_name.' '.$referred->last_name) : $referral->submitted_name }}</h2>
                </div>
                <div class="p-2 bg-emerald-600">
                    <flux:icon name="user-plus" variant="outline" class="w-5 h-5" />
                </div>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-flux-admin::detail-fact icon="user" label="Submitted name" tone="green">{{ $referral->submitted_name }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="phone" label="Submitted phone" tone="green">{{ $referral->submitted_phone }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="envelope" label="Submitted email" tone="indigo">{{ $referral->submitted_email ?: '—' }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="calendar" label="Created" tone="zinc">{{ $referral->created_at?->format('d M Y H:i') }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="identification" label="Matched customer" tone="blue" :href="$referred ? route('flux-admin.customers.show', $referred->id) : null" class="sm:col-span-2">
                        {{ $referred ? '#'.$referred->id.' '.$referred->first_name.' '.$referred->last_name : 'Not matched yet' }}
                    </x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="banknotes" label="Qualifying invoice" tone="purple" class="sm:col-span-2">
                        @if($referral->referred_qualifying_invoice_id)
                            #{{ $referral->referred_qualifying_invoice_id }}
                            · {{ optional($referral->referredQualifyingInvoice?->paid_date)->format('d M Y') }}
                            · £{{ number_format((float) ($referral->referredQualifyingInvoice?->amount ?? 0), 2) }}
                        @else
                            —
                        @endif
                    </x-flux-admin::detail-fact>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($referred)
                        <a href="{{ route('flux-admin.customers.show', $referred->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium bg-emerald-700 text-white hover:bg-emerald-800">Open customer</a>
                    @endif
                    @if($referral->referred_qualifying_booking_id)
                        <a href="{{ route('flux-admin.rentals.show', $referral->referred_qualifying_booking_id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40">Open qualifying booking</a>
                    @endif
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 mb-2">Hire history</p>
                    <ul class="border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($referredHistory as $booking)
                            <li class="flex items-center justify-between gap-3 px-3 py-2 text-sm {{ $loop->even ? 'bg-zinc-50 dark:bg-zinc-950/40' : 'bg-white dark:bg-zinc-900' }}">
                                <a href="{{ route('flux-admin.rentals.show', $booking) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Booking #{{ $booking->id }}</a>
                                <span class="text-xs text-zinc-500">{{ $booking->is_posted ? 'Posted' : 'Intake' }}</span>
                            </li>
                        @empty
                            <li class="px-3 py-2 text-sm text-zinc-500">No bookings.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Checks</h2>
        @if($referral->status === 'review')
            @if($readyToApprove)
                <p class="mt-3 text-sm font-medium text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-2">System is green. This referral is good to approve.</p>
            @else
                <p class="mt-3 text-sm font-medium text-red-800 dark:text-red-200 border border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-3 py-2">Red checks still need to be clear before approval.</p>
            @endif
        @endif
        @if($referral->status === 'approved' && $credit && $credit->status !== 'redeemed' && ! $coveringDirectAward)
            <p class="mt-3 text-sm border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-2">
                Programme points for <strong>this friend</strong> are {{ $referral->pointsStatusLabel() }}.
                They pay one current unpaid week on the referrer’s hire. Each friend can only be used once.
            </p>
        @endif
        @if($coveringDirectAward && $credit?->status !== 'redeemed')
            <p class="mt-3 text-sm border border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 px-3 py-2">
                This friend already received a direct free week (invoice #{{ $coveringDirectAward->awarded_invoice_id }}).
                Do not apply another week. Mark these points as redeemed already.
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

    @if($thisFriendAwards->isNotEmpty() || $credit?->status === 'redeemed')
        <section class="overflow-hidden border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-zinc-900">
            <div class="px-5 py-4 bg-emerald-800 text-white">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-100">Cash on this friend</p>
                <h2 class="mt-1 text-lg font-bold">Free week posted</h2>
                <p class="mt-1 text-sm text-emerald-100">One block for this pair. If staff applied it as a direct gift, programme points for this friend are marked spent on the same week — not shown twice.</p>
            </div>
            <div class="p-5 space-y-4">
                @forelse($thisFriendAwards as $award)
                    @include('flux-admin.partials.rentals.free-week-award-details', ['award' => $award])
                @empty
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Redeemed on invoice #{{ $credit?->redeemed_invoice_id }}. {{ $credit?->updated_at?->format('d M Y H:i') }}.</p>
                @endforelse
            </div>
        </section>
    @endif

    @if($otherDirectAwards->isNotEmpty())
        <section class="overflow-hidden border border-indigo-200 dark:border-indigo-900 bg-white dark:bg-zinc-900">
            <div class="px-5 py-4 bg-indigo-800 text-white">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-100">Other people</p>
                <h2 class="mt-1 text-lg font-bold">Staff direct gifts on someone else</h2>
                <p class="mt-1 text-sm text-indigo-100">These weeks are not this friend. Another friend is another 100.</p>
            </div>
            <div class="p-5 space-y-4">
                @foreach($otherDirectAwards as $award)
                    @include('flux-admin.partials.rentals.free-week-award-details', ['award' => $award])
                @endforeach
            </div>
        </section>
    @endif

    @if($canReview)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 space-y-3">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Review</h2>
            <flux:textarea wire:model="reviewReason" rows="3" placeholder="Reason for approve or disapprove" class="!rounded-none" />
            @error('reviewReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            @error('review_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="flex flex-wrap gap-2 items-center">
                @if($referral->status === 'review' && $credit?->status === 'pending' && $coveringDirectAward)
                    <p class="text-sm">This friend already had a direct free week. This row cannot be approved as a second week.</p>
                    <flux:button size="sm" variant="primary" wire:click="markAlreadyRedeemed" wire:confirm="Mark these points redeemed against the existing direct week?" class="!rounded-none">Mark as redeemed already</flux:button>
                    <flux:button size="sm" variant="danger" wire:click="reject" wire:confirm="Disapprove this referral?" class="!rounded-none">Disapprove</flux:button>
                @elseif($referral->status === 'review' && $credit?->status === 'pending')
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

            @if($referral->status === 'approved' && $credit && $credit->status !== 'redeemed')
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
                    @if($coveringDirectAward)
                        <p class="text-sm">This friend already had a direct free week. Mark these 100 points redeemed against that week. Do not apply a second week on the referrer’s invoice.</p>
                        <flux:button size="sm" variant="primary" wire:click="markAlreadyRedeemed" wire:confirm="Mark these points redeemed against the existing direct week? No extra invoice will be paid." class="!rounded-none">Mark as redeemed already</flux:button>
                        @error('reviewReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    @elseif($redeemableInvoices->isEmpty())
                        <p class="text-sm">No current unpaid week on the referrer’s posted rentals. If this week is already paid, wait for the next unpaid week. Future weeks cannot take a free week yet.</p>
                    @else
                        @if(! $credit->isSpendable())
                            <p class="text-sm">Points are being transferred and will be usable from {{ optional($credit->available_from)->format('d M Y H:i') ?: 'now' }}.</p>
                            <p class="text-sm">Early apply: pick one current unpaid invoice. Explain this to the boss. Approval already covered the background story. Future weeks are not listed.</p>
                        @else
                            <p class="text-sm">Pick one current unpaid invoice on the referrer’s posted rentals. Already-paid, already-rewarded and future weeks are not listed.</p>
                            @if($needsExtraFreeWeekProof)
                                <p class="text-sm text-amber-800 dark:text-amber-200">This person already has a free week (programme or direct). Another friend’s 100 is a separate reward. Explain why this extra week is being given.</p>
                            @endif
                        @endif
                        <select wire:model="redeemInvoiceId" class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Choose invoice</option>
                            @foreach($redeemableInvoices as $invoice)
                                <option value="{{ $invoice->id }}">#{{ $invoice->id }} · booking {{ $invoice->booking_id }} · {{ optional($invoice->invoice_date)->format('d M Y') }} · £{{ number_format((float) $invoice->amount, 2) }}</option>
                            @endforeach
                        </select>
                        @error('redeemInvoiceId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @if(! $credit->isSpendable() || $needsExtraFreeWeekProof)
                            <flux:input wire:model="releaseReason" placeholder="{{ $needsExtraFreeWeekProof && $credit->isSpendable() ? 'Explain this extra free week for the boss' : 'Explain this early apply for the boss' }}" class="!rounded-none" />
                            @error('releaseReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            @error('release_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @endif
                        @if(! $credit->isSpendable())
                            <flux:button size="sm" variant="primary" wire:click="releaseEarly" wire:confirm="Apply this one-time free week now?" class="!rounded-none">Release early and apply</flux:button>
                        @else
                            <flux:button size="sm" variant="primary" wire:click="redeem" wire:confirm="Apply this one-time free week now?" class="!rounded-none">Apply free week</flux:button>
                        @endif
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
