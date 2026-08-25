<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Rental referrals</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Programme chain, staff direct free weeks, and the applied reward transactions. Separate from Club.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if(\App\Support\RentingReferralAccess::canInvestigate())
                <a href="{{ route('flux-admin.rental-referral-investigation.index') }}" wire:navigate>
                    <flux:button size="sm" variant="primary" class="!rounded-none">Director investigation</flux:button>
                </a>
            @endif
            <a href="{{ route('flux-admin.rental-operations.index') }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Rentals home</a>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <flux:button size="sm" variant="{{ $view === 'programme' ? 'primary' : 'ghost' }}" wire:click="setView('programme')" class="!rounded-none">Programme</flux:button>
        <flux:button size="sm" variant="{{ $view === 'direct' ? 'primary' : 'ghost' }}" wire:click="setView('direct')" class="!rounded-none">Direct</flux:button>
        <flux:button size="sm" variant="{{ $view === 'all' ? 'primary' : 'ghost' }}" wire:click="setView('all')" class="!rounded-none">All applied</flux:button>
    </div>

    <p class="text-sm text-zinc-500 dark:text-zinc-400">These boxes stay the same on Programme, Direct and All applied. The buttons only change the list underneath.</p>

    @php
        $weeksGiven = (int) ($freeWeekMetrics['programme'] ?? 0) + (int) ($freeWeekMetrics['direct'] ?? 0);
        $poundsGiven = (float) ($freeWeekMetrics['programme_value'] ?? 0) + (float) ($freeWeekMetrics['direct_value'] ?? 0);
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-flux-admin::stat-card
            label="Weeks given"
            :value="number_format($weeksGiven)"
            hint="Every free week actually posted, programme and staff direct together. One friend still counts as one week."
            icon="check-circle"
            colour="green"
        />
        <x-flux-admin::stat-card
            label="£ given"
            :value="'£'.number_format($poundsGiven, 2)"
            hint="Rent we did not take for those weeks. Programme and direct added together, not double-counted."
            icon="currency-pound"
            colour="purple"
        />
        <x-flux-admin::stat-card
            label="Programme weeks"
            :value="number_format($freeWeekMetrics['programme'])"
            hint="Weeks applied from a chained friend referral (100 points used)."
            icon="users"
            colour="blue"
        />
        <x-flux-admin::stat-card
            label="Direct weeks"
            :value="number_format($freeWeekMetrics['direct'])"
            hint="Weeks staff applied by hand. If that friend is also on the programme, those 100 points are marked spent on the same week."
            icon="bolt"
            colour="indigo"
        />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-flux-admin::stat-card
            label="Waiting for staff"
            :value="number_format($metrics['review'])"
            hint="Friends who have paid a week. Staff still need to approve or refuse."
            icon="clipboard-document-check"
            colour="amber"
        />
        <x-flux-admin::stat-card
            label="Points ready"
            :value="number_format($metrics['available_points'])"
            hint="Approved 100s not yet applied to a current unpaid week. Another friend = another 100."
            icon="check-circle"
            colour="green"
        />
        <x-flux-admin::stat-card
            label="Points not ready"
            :value="number_format($metrics['pending_points'])"
            hint="100s after a friend qualified, still in review or in the wait. Not usable on an invoice yet."
            icon="clock"
            colour="blue"
        />
        <x-flux-admin::stat-card
            label="Need a look"
            :value="number_format($metrics['warnings'])"
            hint="Programme rows with a warning (duplicate, already rented, similar name)."
            icon="exclamation-triangle"
            colour="red"
        />
    </div>

    @if($canCreate)
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Add referral (staff)</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">The referrer must already have one paid weekly invoice. Referral must be recorded before the friend’s hire starts.</p>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <flux:input wire:model.live.debounce.300ms="newReferrerSearch" placeholder="Search referrer…" variant="filled" class="!rounded-none" />
                    @if($newReferrerId)
                        <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Selected customer #{{ $newReferrerId }}</p>
                    @endif
                    @if($referrerChoices)
                        <div class="mt-1 border border-zinc-200 dark:border-zinc-700 max-h-40 overflow-y-auto">
                            @foreach($referrerChoices as $choice)
                                <button type="button" wire:click="$set('newReferrerId', {{ $choice->id }})" class="block w-full text-left px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    {{ $choice->first_name }} {{ $choice->last_name }} · {{ $choice->phone }} · #{{ $choice->id }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @error('newReferrerId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <flux:input wire:model="newName" placeholder="Friend’s name" variant="filled" class="!rounded-none" />
                <flux:input wire:model="newPhone" placeholder="07 mobile" variant="filled" class="!rounded-none" />
                <flux:input wire:model="newEmail" placeholder="Email (optional)" variant="filled" class="!rounded-none" />
            </div>
            @error('newName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @error('newPhone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <div class="mt-3">
                <flux:button size="sm" variant="primary" wire:click="createReferral" class="!rounded-none">Save referral</flux:button>
            </div>
        </div>
    @endif

    <div class="flux-admin-toolbar mb-0 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ $showingAwards ? 'Search hirer, referrer, invoice, booking, transaction…' : 'Search code, name, phone, referrer…' }}"
                    variant="filled"
                    class="!rounded-none"
                />
            </div>
            <div class="min-w-0 w-full sm:w-48">
                @if($showingAwards)
                    <flux:select wire:model.live="status" class="!rounded-none">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="redeemed">Redeemed</flux:select.option>
                        <flux:select.option value="reversed">Reversed</flux:select.option>
                    </flux:select>
                @else
                    <flux:select wire:model.live="status" class="!rounded-none">
                        <flux:select.option value="">Any stage</flux:select.option>
                        <flux:select.option value="in_progress">In the chain</flux:select.option>
                        <flux:select.option value="review">Waiting for staff</flux:select.option>
                        <flux:select.option value="approved">Ready to apply</flux:select.option>
                        <flux:select.option value="redeemed">Week posted</flux:select.option>
                        <flux:select.option value="refused">Refused / cancelled</flux:select.option>
                    </flux:select>
                @endif
            </div>
        </div>
    </div>

    <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            @if($showingAwards)
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Type</flux:table.column>
                        <flux:table.column>Hirer</flux:table.column>
                        <flux:table.column>Selected referrer</flux:table.column>
                        <flux:table.column>Invoice</flux:table.column>
                        <flux:table.column>Booking</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Transaction</flux:table.column>
                        <flux:table.column>Applied by</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($rows as $row)
                            @php
                                $hirerName = trim(($row->hirer?->first_name ?? '').' '.($row->hirer?->last_name ?? ''));
                                $referrerName = trim(($row->selectedReferrer?->first_name ?? '').' '.($row->selectedReferrer?->last_name ?? ''));
                            @endphp
                            <flux:table.row wire:key="fw-{{ $row->id }}">
                                <flux:table.cell class="whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y H:i') }}</flux:table.cell>
                                <flux:table.cell>
                                    @include('flux-admin.partials.rentals.status-pill', ['label' => $row->payoutStatusLabel(), 'tone' => $row->payoutStatusTone()])
                                </flux:table.cell>
                                <flux:table.cell>
                                    @include('flux-admin.partials.rentals.status-pill', [
                                        'label' => $row->sourceLabel(),
                                        'tone' => $row->isDirect() ? 'blue' : 'green',
                                    ])
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($row->hirer)
                                        <a href="{{ route('flux-admin.customers.show', $row->hirer_customer_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $hirerName ?: '#'.$row->hirer_customer_id }}</a>
                                    @else
                                        #{{ $row->hirer_customer_id }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($row->selectedReferrer)
                                        <a href="{{ route('flux-admin.customers.show', $row->selected_referrer_customer_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $referrerName ?: '#'.$row->selected_referrer_customer_id }}</a>
                                    @else
                                        #{{ $row->selected_referrer_customer_id }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="text-sm">#{{ $row->awarded_invoice_id }}</flux:table.cell>
                                <flux:table.cell>
                                    <a href="{{ route('flux-admin.rentals.show', $row->awarded_booking_id) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">#{{ $row->awarded_booking_id }}</a>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm whitespace-nowrap">£{{ number_format((float) $row->amount, 2) }}</flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $row->awarded_transaction_id ? '#'.$row->awarded_transaction_id : '—' }}</flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $row->appliedBy?->full_name ?: ($row->appliedBy?->email ?: '—') }}</flux:table.cell>
                                <flux:table.cell>
                                    <button type="button" wire:click="toggleAward({{ $row->id }})" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $expandedAwardId === (int) $row->id ? 'Hide details' : 'Details' }}
                                    </button>
                                </flux:table.cell>
                            </flux:table.row>
                            @if($expandedAwardId === (int) $row->id)
                                <flux:table.row wire:key="fw-detail-{{ $row->id }}">
                                    <flux:table.cell colspan="11">
                                        <div class="py-2">
                                            @include('flux-admin.partials.rentals.free-week-award-details', ['award' => $row])
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endif
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="11" class="text-sm text-zinc-500">
                                    {{ $view === 'direct' ? 'No direct free weeks applied yet.' : 'No applied free weeks yet.' }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Referrer</flux:table.column>
                        <flux:table.column>Referred</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Points</flux:table.column>
                        <flux:table.column>Available from</flux:table.column>
                        <flux:table.column>Source</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($rows as $row)
                            @php $credit = $row->credit(); @endphp
                            <flux:table.row wire:key="rr-{{ $row->id }}">
                                <flux:table.cell class="whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y') }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($row->referrer)
                                        <a href="{{ route('flux-admin.customers.show', $row->referrer_customer_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $row->referrer->first_name }} {{ $row->referrer->last_name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div>{{ $row->submitted_name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $row->submitted_phone }}@if($row->referred) · matched #{{ $row->referred_customer_id }}@endif</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @include('flux-admin.partials.rentals.status-pill', ['label' => $row->staffStatusLabel(), 'tone' => $row->staffStatusTone()])
                                    @if($row->hasWarning())
                                        <span class="ml-1 text-xs text-amber-700 dark:text-amber-400">warning</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @include('flux-admin.partials.rentals.status-pill', ['label' => $row->pointsStatusLabel(), 'tone' => $row->pointsStatusTone()])
                                </flux:table.cell>
                                <flux:table.cell class="text-sm whitespace-nowrap">{{ $credit?->available_from?->format('d M Y') ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $row->source }}</flux:table.cell>
                                <flux:table.cell>
                                    <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Investigate</a>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="text-sm text-zinc-500">No referrals yet.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800">
            {{ $rows->links() }}
        </div>
    </div>
</div>
