@php
    $name = function ($customer, $fallback = '—') {
        if (! $customer) {
            return $fallback;
        }
        $label = trim(($customer->first_name ?? '').' '.($customer->last_name ?? ''));

        return $label !== '' ? $label : $fallback;
    };
    $weeksGiven = (int) ($metrics['programme_weeks'] ?? 0) + (int) ($metrics['direct_weeks'] ?? 0);
@endphp

<div class="space-y-5">
    <x-flux-admin::summary-header
        title="Referral investigation"
        subtitle="Director view of chained friends and staff direct free weeks. Numbers follow the filters. Staff day-to-day list stays on Rentals referrals."
        :backUrl="route('flux-admin.rental-referrals.index')"
        backLabel="Staff referral list"
        :badges="[
            ['label' => 'Thiago / Super Admin', 'color' => 'zinc'],
            ['label' => 'Programme + Direct', 'color' => 'blue'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ route('flux-admin.rental-operations.index') }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Rentals home</a>
        </x-slot:actions>
    </x-flux-admin::summary-header>

    <div class="flex flex-wrap gap-2">
        <flux:button size="sm" variant="{{ $kind === 'all' ? 'primary' : 'ghost' }}" wire:click="setKind('all')" class="!rounded-none">All records</flux:button>
        <flux:button size="sm" variant="{{ $kind === 'programme' ? 'primary' : 'ghost' }}" wire:click="setKind('programme')" class="!rounded-none">Programme chain</flux:button>
        <flux:button size="sm" variant="{{ $kind === 'direct' ? 'primary' : 'ghost' }}" wire:click="setKind('direct')" class="!rounded-none">Staff direct</flux:button>
        <span class="hidden sm:inline-block w-px bg-zinc-200 dark:bg-zinc-700 self-stretch"></span>
        <flux:button size="sm" variant="ghost" wire:click="setPreset('posted')" class="!rounded-none">Week posted</flux:button>
        <flux:button size="sm" variant="ghost" wire:click="setPreset('review')" class="!rounded-none">Waiting for staff</flux:button>
        <flux:button size="sm" variant="ghost" wire:click="setPreset('month')" class="!rounded-none">This month</flux:button>
        <flux:button size="sm" variant="ghost" wire:click="setPreset('days30')" class="!rounded-none">Last 30 days</flux:button>
        @if($filtersActive)
            <flux:button size="sm" variant="danger" wire:click="resetFilters" class="!rounded-none">Clear filters</flux:button>
        @endif
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 border-l-emerald-600">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">£ given</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $metrics['pounds_given'], 2) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Rent not taken on matching free weeks.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 border-l-red-600">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">£ reversed</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $metrics['pounds_reversed'], 2) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Gifted week invoice later marked unpaid.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 border-l-indigo-600">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Weeks posted</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($weeksGiven) }}</p>
            <p class="mt-1 text-xs text-zinc-500">{{ number_format($metrics['programme_weeks']) }} programme · {{ number_format($metrics['direct_weeks']) }} direct.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 border-l-amber-500">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Waiting for staff</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($metrics['waiting_review']) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Friend paid a week. Approve or refuse.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 border-l-red-500">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Need a look</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($metrics['warnings']) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Duplicate, already rented, or similar name.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <p class="text-xs font-medium text-zinc-500">Programme rows</p>
            <p class="mt-1 text-xl font-bold text-sky-700 dark:text-sky-300">{{ number_format($metrics['programme_rows']) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Friends in the chain, matching filters.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <p class="text-xs font-medium text-zinc-500">Points ready</p>
            <p class="mt-1 text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($metrics['ready_points']) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Approved 100s not yet on an invoice.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <p class="text-xs font-medium text-zinc-500">Points not ready</p>
            <p class="mt-1 text-xl font-bold text-sky-700 dark:text-sky-300">{{ number_format($metrics['pending_points']) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Still in review or in the wait.</p>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <p class="text-xs font-medium text-zinc-500">Points spent / early</p>
            <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">{{ number_format($metrics['spent_points']) }} <span class="text-sm font-medium text-zinc-500">/ {{ number_format($metrics['early_releases']) }}</span></p>
            <p class="mt-1 text-xs text-zinc-500">Spent on a week · released before 14 days.</p>
        </div>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 space-y-5">
        <div>
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Find a record</h2>
            <p class="mt-1 text-xs text-zinc-500">Each control does one job. Kind is the tabs above. Stage is where the row sits. The rest are flags, cash and dates.</p>
        </div>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Name, phone, code, invoice, booking, txn, customer #…" variant="filled" class="!rounded-none" />
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Stage</p>
                <flux:select wire:model.live="stage" class="!rounded-none">
                    <flux:select.option value="">Any stage</flux:select.option>
                    <flux:select.option value="waiting">In the chain</flux:select.option>
                    <flux:select.option value="review">Waiting for staff</flux:select.option>
                    <flux:select.option value="ready">Ready to apply</flux:select.option>
                    <flux:select.option value="posted">Week posted</flux:select.option>
                    <flux:select.option value="reversed">Week reversed</flux:select.option>
                    <flux:select.option value="refused">Refused / cancelled</flux:select.option>
                </flux:select>
            </div>
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Recorded via</p>
                <flux:select wire:model.live="source" class="!rounded-none">
                    <flux:select.option value="">Any source</flux:select.option>
                    <flux:select.option value="portal">Portal</flux:select.option>
                    <flux:select.option value="admin">Staff</flux:select.option>
                    <flux:select.option value="link">Share link</flux:select.option>
                </flux:select>
            </div>
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Applied / reviewed by</p>
                <flux:select wire:model.live="staffId" class="!rounded-none">
                    <flux:select.option value="">Any staff</flux:select.option>
                    @foreach($staffChoices as $staff)
                        <flux:select.option value="{{ $staff->id }}">{{ $staff->full_name ?: $staff->email }} · #{{ $staff->id }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">£ given range</p>
                <div class="grid grid-cols-2 gap-2">
                    <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="amountMin" placeholder="Min" variant="filled" class="!rounded-none" />
                    <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="amountMax" placeholder="Max" variant="filled" class="!rounded-none" />
                </div>
            </div>
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">From</p>
                <flux:input type="date" wire:model.live="from" variant="filled" class="!rounded-none" />
            </div>
            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">To</p>
                <flux:input type="date" wire:model.live="to" variant="filled" class="!rounded-none" />
            </div>
            <div class="xl:col-span-2">
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Flags</p>
                <div class="flex flex-wrap gap-2">
                    <flux:button size="sm" variant="{{ $warning === 'yes' ? 'primary' : 'ghost' }}" wire:click="toggleFlag('warning')" class="!rounded-none">Has a warning</flux:button>
                    <flux:button size="sm" variant="{{ $early === 'yes' ? 'primary' : 'ghost' }}" wire:click="toggleFlag('early')" class="!rounded-none">Released early</flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-end justify-between gap-3">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Records</h2>
            <p class="text-xs text-zinc-500">{{ number_format($rows->total()) }} matching · programme cards nest cash on that friend · direct cards are staff gifts</p>
        </div>

        @forelse($rows as $row)
            @if($row['kind'] === 'direct')
                @php $award = $row['award']; @endphp
                <article wire:key="{{ $row['key'] }}" class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 border-l-4 border-l-indigo-600">
                    <div class="p-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                        <div class="min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                @include('flux-admin.partials.rentals.status-pill', ['label' => 'Direct', 'tone' => 'indigo'])
                                @include('flux-admin.partials.rentals.status-pill', ['label' => $award->payoutStatusLabel(), 'tone' => $award->payoutStatusTone()])
                                <span class="text-lg font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $award->amount, 2) }}</span>
                            </div>
                            <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                Hirer
                                <a href="{{ route('flux-admin.customers.show', $award->hirer_customer_id) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $name($award->hirer, '#'.$award->hirer_customer_id) }}</a>
                                <span class="text-zinc-400">· named</span>
                                <a href="{{ route('flux-admin.customers.show', $award->selected_referrer_customer_id) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $name($award->selectedReferrer, '#'.$award->selected_referrer_customer_id) }}</a>
                            </p>
                            <p class="text-xs text-zinc-500">
                                Invoice #{{ $award->awarded_invoice_id }}
                                · Booking #{{ $award->awarded_booking_id }}
                                · Txn {{ $award->awarded_transaction_id ? '#'.$award->awarded_transaction_id : '—' }}
                                · {{ $award->created_at?->format('d M Y H:i') }}
                                · {{ $award->appliedBy?->full_name ?: 'staff' }}
                            </p>
                        </div>
                        <flux:button size="sm" variant="ghost" wire:click="toggle('{{ $row['key'] }}')" class="!rounded-none shrink-0">{{ $openKey === $row['key'] ? 'Hide' : 'Investigate' }}</flux:button>
                    </div>
                    @if($openKey === $row['key'])
                        <div class="px-4 pb-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                            @include('flux-admin.partials.rentals.free-week-award-details', ['award' => $award])
                        </div>
                    @endif
                </article>
            @else
                @php
                    $referral = $row['referral'];
                    $awards = $row['awards'];
                    $directAwards = $awards->where('source', 'direct');
                    $programmeAwards = $awards->where('source', '!=', 'direct');
                    $cash = (float) $awards->sum('amount');
                @endphp
                <article wire:key="{{ $row['key'] }}" class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 border-l-4 {{ $referral->hasWarning() ? 'border-l-red-600' : 'border-l-sky-600' }}">
                    <div class="p-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                        <div class="min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                @include('flux-admin.partials.rentals.status-pill', ['label' => 'Programme', 'tone' => 'blue'])
                                @include('flux-admin.partials.rentals.status-pill', ['label' => $referral->staffStatusLabel(), 'tone' => $referral->staffStatusTone()])
                                @include('flux-admin.partials.rentals.status-pill', ['label' => $referral->pointsStatusLabel(), 'tone' => $referral->pointsStatusTone()])
                                @if($referral->hasWarning())
                                    @include('flux-admin.partials.rentals.status-pill', ['label' => 'Warning', 'tone' => 'red'])
                                @endif
                                @if($directAwards->isNotEmpty())
                                    @include('flux-admin.partials.rentals.status-pill', ['label' => 'Covered by direct', 'tone' => 'indigo'])
                                @endif
                                @if($cash > 0)
                                    <span class="text-lg font-bold text-zinc-900 dark:text-white">£{{ number_format($cash, 2) }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                Referrer
                                <a href="{{ route('flux-admin.customers.show', $referral->referrer_customer_id) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $name($referral->referrer, '#'.$referral->referrer_customer_id) }}</a>
                                <span class="text-zinc-400">→ friend</span>
                                @if($referral->referred)
                                    <a href="{{ route('flux-admin.customers.show', $referral->referred_customer_id) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $name($referral->referred) }}</a>
                                @else
                                    <span class="font-medium">{{ $referral->submitted_name }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-zinc-500">
                                #{{ $referral->id }} · {{ $referral->referral_code }}
                                · {{ $referral->submitted_phone }}
                                · {{ $referral->created_at?->format('d M Y H:i') }}
                                @if($referral->referred_qualifying_invoice_id)
                                    · Qualifying invoice #{{ $referral->referred_qualifying_invoice_id }}
                                @endif
                            </p>
                        </div>
                        <flux:button size="sm" variant="ghost" wire:click="toggle('{{ $row['key'] }}')" class="!rounded-none shrink-0">{{ $openKey === $row['key'] ? 'Hide' : 'Investigate' }}</flux:button>
                    </div>
                    @if($openKey === $row['key'])
                        <div class="px-4 pb-4 border-t border-zinc-100 dark:border-zinc-800 pt-4 space-y-4">
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                <a href="{{ route('flux-admin.rental-referrals.show', $referral) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Open full referral #{{ $referral->id }}</a>
                                @if($referral->referrer)
                                    <a href="{{ route('flux-admin.customers.show', $referral->referrer) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Referrer customer</a>
                                @endif
                                @if($referral->referred)
                                    <a href="{{ route('flux-admin.customers.show', $referral->referred) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Friend customer</a>
                                @endif
                                @if($referral->referred_qualifying_booking_id)
                                    <a href="{{ route('flux-admin.rentals.show', $referral->referred_qualifying_booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Friend qualifying booking #{{ $referral->referred_qualifying_booking_id }}</a>
                                @endif
                            </div>
                            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                                <div><dt class="text-xs text-zinc-500">Recorded via</dt><dd>{{ ucfirst((string) $referral->source) }}</dd></div>
                                <div><dt class="text-xs text-zinc-500">Reviewed</dt><dd>{{ $referral->reviewed_at?->format('d M Y H:i') ?: '—' }} · {{ $referral->reviewedBy?->full_name ?: '—' }}</dd></div>
                                <div><dt class="text-xs text-zinc-500">Reason</dt><dd>{{ $referral->review_reason ?: '—' }}</dd></div>
                                <div><dt class="text-xs text-zinc-500">Friend email</dt><dd>{{ $referral->submitted_email ?: ($referral->referred?->email ?: '—') }}</dd></div>
                            </dl>
                            @if($referral->hasWarning())
                                <div class="border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-3 text-sm text-red-900 dark:text-red-100 space-y-1">
                                    @foreach((array) $referral->warnings as $warningKey => $warningValue)
                                        <p>{{ match ((string) $warningKey) {
                                            'created_after_start' => 'Recorded after the friend’s hire started.',
                                            'multiple_matches' => 'More than one customer matched the phone or email.',
                                            'similar_name_dob' => 'Similar name or date of birth on file.',
                                            'self_referral' => 'Looks like a self-referral.',
                                            'already_rented' => 'This friend has already rented.',
                                            'already_attributed' => 'This friend already used a free week.',
                                            default => is_string($warningKey) ? ucfirst(str_replace('_', ' ', $warningKey)).'.' : 'Warning on this row.',
                                        } }}</p>
                                    @endforeach
                                </div>
                            @endif
                            @forelse($programmeAwards as $award)
                                @include('flux-admin.partials.rentals.free-week-award-details', ['award' => $award])
                            @empty
                                <p class="text-xs text-zinc-500">No programme free week posted on this friend yet.</p>
                            @endforelse
                            @if($directAwards->isNotEmpty())
                                <p class="text-xs text-indigo-800 dark:text-indigo-200">Staff also posted a direct week for this pair. That cash is a Direct card in this list — one friend still equals one week.</p>
                            @endif
                        </div>
                    @endif
                </article>
            @endif
        @empty
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 text-sm text-zinc-500">
                No matching records. Clear filters or search another person, invoice, booking or transaction.
            </div>
        @endforelse

        @if($rows->hasPages())
            <div>{{ $rows->links() }}</div>
        @endif
    </div>
</div>
