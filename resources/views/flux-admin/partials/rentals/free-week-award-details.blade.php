@php
    /** @var \App\Models\RentingFreeWeekAward $award */
    $hirerName = trim(($award->hirer?->first_name ?? '').' '.($award->hirer?->last_name ?? ''));
    $referrerName = trim(($award->selectedReferrer?->first_name ?? '').' '.($award->selectedReferrer?->last_name ?? ''));
    $appliedName = $award->appliedBy?->full_name ?: ($award->appliedBy?->email ?: '—');
    $txnDate = $award->awardedTransaction?->transaction_date;
    $txnDateLabel = $txnDate ? \Carbon\Carbon::parse($txnDate)->format('d M Y') : null;
    $paidDate = $award->awardedInvoice?->paid_date;
    $paidAt = $paidDate
        ? \Carbon\Carbon::parse($paidDate)->format('d M Y')
        : ($txnDateLabel ?: $award->created_at?->format('d M Y H:i'));
    $hirerDiffers = (int) $award->hirer_customer_id !== (int) $award->selected_referrer_customer_id;
    $direct = $award->isDirect();
    $band = $direct ? 'bg-indigo-700 text-white' : 'bg-sky-700 text-white';
    $frame = $direct ? 'border-indigo-200 dark:border-indigo-900' : 'border-sky-200 dark:border-sky-900';
    $panel = $direct ? 'bg-indigo-50/70 dark:bg-indigo-950/20' : 'bg-sky-50/70 dark:bg-sky-950/20';
@endphp
<div class="overflow-hidden border {{ $frame }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4 {{ $band }}">
        <div class="flex flex-wrap items-center gap-2">
            @include('flux-admin.partials.rentals.status-pill', ['label' => $award->payoutStatusLabel(), 'tone' => $award->payoutStatusTone()])
            @include('flux-admin.partials.rentals.status-pill', [
                'label' => $award->sourceLabel(),
                'tone' => $direct ? 'indigo' : 'blue',
            ])
            <p class="text-sm {{ $direct ? 'text-indigo-100' : 'text-sky-100' }}">
                @if($direct && $award->referral_id)
                    Staff gift — programme points for this friend marked redeemed
                @elseif($direct)
                    Staff gift — no matching programme friend yet
                @else
                    Programme points spent
                @endif
            </p>
        </div>
        <p class="text-3xl font-bold leading-none">£{{ number_format((float) $award->amount, 2) }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2">
        <div class="p-5 space-y-3 {{ $panel }}">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-300">
                <flux:icon name="banknotes" variant="outline" class="w-4 h-4" />
                Week paid
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::detail-fact icon="document-text" label="Invoice" tone="purple">
                    #{{ $award->awarded_invoice_id }} · {{ optional($award->awardedInvoice?->invoice_date)->format('d M Y') ?: '—' }}
                </x-flux-admin::detail-fact>
                <x-flux-admin::detail-fact icon="calendar" label="Paid date" tone="green">
                    {{ $paidAt ?: '—' }}
                </x-flux-admin::detail-fact>
                <x-flux-admin::detail-fact icon="key" label="Booking" tone="blue" :href="route('flux-admin.rentals.show', $award->awarded_booking_id)">
                    #{{ $award->awarded_booking_id }}
                </x-flux-admin::detail-fact>
                <x-flux-admin::detail-fact icon="queue-list" label="Transaction" tone="indigo">
                    {{ $award->awarded_transaction_id ? '#'.$award->awarded_transaction_id : '—' }}{{ $txnDateLabel ? ' · '.$txnDateLabel : '' }}
                </x-flux-admin::detail-fact>
                <x-flux-admin::detail-fact icon="user" label="Applied" class="sm:col-span-2">
                    {{ $award->created_at?->format('d M Y H:i') ?: '—' }} · {{ $appliedName }}
                </x-flux-admin::detail-fact>
            </div>
        </div>
        <div class="p-5 space-y-3 bg-white dark:bg-zinc-900 border-t lg:border-t-0 lg:border-l border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-300">
                <flux:icon name="users" variant="outline" class="w-4 h-4" />
                People
            </div>
            <div class="grid grid-cols-1 gap-3">
                <x-flux-admin::detail-fact
                    icon="user"
                    label="Hirer (week credited to)"
                    tone="green"
                    :href="$award->hirer ? route('flux-admin.customers.show', $award->hirer_customer_id) : null"
                >
                    {{ $hirerName !== '' ? $hirerName : '#'.$award->hirer_customer_id }}
                </x-flux-admin::detail-fact>
                <x-flux-admin::detail-fact
                    icon="user-plus"
                    label="Named as"
                    tone="blue"
                    :href="$award->selectedReferrer ? route('flux-admin.customers.show', $award->selected_referrer_customer_id) : null"
                >
                    {{ $referrerName !== '' ? $referrerName : '#'.$award->selected_referrer_customer_id }}
                </x-flux-admin::detail-fact>
                @if($award->referral_id)
                    <x-flux-admin::detail-fact
                        icon="link"
                        label="Programme row"
                        tone="indigo"
                        :href="route('flux-admin.rental-referrals.show', $award->referral_id)"
                    >
                        #{{ $award->referral_id }}{{ $award->referral?->referral_code ? ' · '.$award->referral->referral_code : '' }}
                    </x-flux-admin::detail-fact>
                @endif
            </div>
            @if($hirerDiffers && $award->referral_id)
                <p class="text-xs border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-100 px-3 py-2">New customer received the week. Named referrer’s points for this friend are redeemed. One friend = one free week.</p>
            @elseif($hirerDiffers)
                <p class="text-xs border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/30 text-amber-900 dark:text-amber-100 px-3 py-2">New customer received the week. If this is the friend they referred, mark that programme row redeemed already — do not apply a second week.</p>
            @endif
        </div>
    </div>

    @if($award->staff_proof || $award->awardedTransaction?->notes)
        <div class="px-5 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-amber-50 dark:bg-amber-950/20 space-y-1">
            @if($award->staff_proof)
                <p class="text-sm text-zinc-800 dark:text-zinc-200"><span class="font-semibold">Staff explanation:</span> {{ $award->staff_proof }}</p>
            @endif
            @if($award->awardedTransaction?->notes)
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $award->awardedTransaction->notes }}</p>
            @endif
        </div>
    @endif

    <details class="px-5 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/40 text-xs">
        <summary class="cursor-pointer font-medium text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white">Named person’s last paid weeks (snapshot, not the gifted week)</summary>
        <div class="mt-3">
            @include('flux-admin.partials.rentals.referrer-paid-invoices', [
                'invoices' => $award->selected_paid_invoices ?? [],
                'missing' => empty($award->selected_paid_invoices),
                'message' => $award->eligibility_note,
                'bookingId' => $award->selected_referrer_booking_id,
            ])
        </div>
    </details>
</div>
