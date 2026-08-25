<div class="space-y-6">
    @php
        $heads = [
            'bg-red-800',
            'bg-zinc-800',
            'bg-sky-800',
            'bg-indigo-800',
        ];
        $summary = $report['summary'] ?? [];
        $outstandingTotal = (float) collect($report['accounts'])->sum('outstanding');
    @endphp

    <x-flux-admin::summary-header
        title="Weekly chase report"
        subtitle="One rental at a time: who still owes, what is unpaid, and what staff wrote this week."
        :backUrl="route('flux-admin.rental-operations.index')"
        backLabel="Back to rentals"
    />

    <div class="flux-admin-panel border border-zinc-200 dark:border-zinc-800 p-5 space-y-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Reporting week</p>
            <select wire:model.live="periodKey" class="mt-1.5 w-full max-w-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100">
                @foreach($periods as $period)
                    <option value="{{ $period['key'] }}">{{ $period['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-wrap gap-2">
            <flux:button size="sm" variant="primary" wire:click="downloadPdf" class="!rounded-none">Download PDF</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="emailDirector" wire:confirm="Email this snapshot to the director and copy customer service?" class="!rounded-none">Email director</flux:button>
        </div>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">Director: thiago@neguinhomotors.co.uk · CC: customerservice@neguinhomotors.co.uk</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-flux-admin::stat-card label="Still unpaid" :value="'£'.number_format($outstandingTotal, 2)" icon="banknotes" colour="red" />
        <x-flux-admin::stat-card label="Accounts" :value="number_format((int) ($summary['customers'] ?? 0))" icon="users" colour="amber" />
        <x-flux-admin::stat-card label="Follow-up notes" :value="number_format((int) ($summary['entries'] ?? 0))" icon="chat-bubble-left-ellipsis" colour="blue" />
        <x-flux-admin::stat-card label="Week" :value="$report['start']->format('d M').' – '.$report['end']->format('d M Y')" icon="calendar" colour="zinc" />
    </div>

    <div class="border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/30 p-5 space-y-4">
        <p class="text-xs font-medium text-sky-900 dark:text-sky-200">{{ $report['start']->format('l j F Y, H:i') }} to {{ $report['end']->format('l j F Y, H:i') }}</p>
        <p class="text-sm text-zinc-800 dark:text-sky-100 leading-relaxed">{{ $report['intro'] }}</p>
        @if(($summary['by_staff'] ?? collect())->isNotEmpty())
            <div class="overflow-x-auto border border-sky-200 dark:border-sky-900 bg-white dark:bg-zinc-900">
                <table class="w-full max-w-lg text-sm">
                    <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="text-left px-4 py-2.5 font-semibold">Staff</th>
                            <th class="text-left px-4 py-2.5 font-semibold">Follow-up notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['by_staff'] as $staff)
                            <tr class="border-t border-zinc-200 dark:border-zinc-800">
                                <td class="px-4 py-2.5 text-zinc-900 dark:text-zinc-100">{{ $staff['name'] }}</td>
                                <td class="px-4 py-2.5 font-semibold text-zinc-900 dark:text-zinc-100">{{ $staff['count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @forelse($report['accounts'] as $account)
        @php
            $head = $heads[$loop->index % 4];
            $noteCount = $account['notes']->count();
        @endphp
        <div class="flux-admin-panel overflow-hidden border border-zinc-200 dark:border-zinc-700">
            <div class="flux-admin-chase-head flux-admin-on-dark {{ $head }} !text-white">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-white/80">Chase account</p>
                        <h2 class="mt-1 text-xl font-bold leading-snug">
                            <a href="{{ route('flux-admin.rentals.show', $account['booking_id']) }}" class="!text-white hover:!text-white underline decoration-white/50 hover:decoration-white">
                                Rental #{{ $account['booking_id'] }} · {{ $account['customer'] }}
                            </a>
                        </h2>
                    </div>
                    <div class="shrink-0 bg-black/25 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-white/80">Still unpaid</p>
                        <p class="mt-0.5 text-2xl font-bold">£{{ number_format($account['outstanding'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="flux-admin-chase-body space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <x-flux-admin::detail-fact icon="hashtag" label="Customer" tone="blue">#{{ $account['customer_id'] ?: '—' }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="phone" label="Phone" tone="green">{{ $account['phone'] }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="envelope" label="Email" tone="indigo">{{ $account['email'] }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="truck" label="Vehicle" tone="zinc">{{ $account['registration'] }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="banknotes" label="Weekly rent" tone="amber">{{ $account['weekly_rent'] ? '£'.number_format((float) $account['weekly_rent'], 2) : '—' }}</x-flux-admin::detail-fact>
                    <x-flux-admin::detail-fact icon="calendar" label="Oldest unpaid" tone="red">{{ $account['oldest_due'] }}</x-flux-admin::detail-fact>
                </div>

                <div class="border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/30 px-4 py-4 text-sm text-zinc-800 dark:text-sky-100 leading-relaxed">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-800 dark:text-sky-200 mb-1.5">This week</p>
                    <p>{{ $account['story'] }}</p>
                    <p class="mt-2 text-xs text-sky-900/80 dark:text-sky-200/80">{{ $noteCount }} follow-up {{ $noteCount === 1 ? 'note' : 'notes' }}</p>
                </div>

                @foreach($account['invoices'] as $invoice)
                    @php $invoiceNotes = collect($invoice['notes']); @endphp
                    <div class="border border-zinc-200 dark:border-zinc-700 border-l-4 border-l-red-600">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-200 dark:border-zinc-800">
                            <div class="flex flex-wrap items-center gap-2">
                                @include('flux-admin.partials.rentals.status-pill', ['label' => 'Unpaid', 'tone' => 'red'])
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">Invoice #{{ $invoice['id'] }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $invoice['date'] }}</p>
                            </div>
                            <p class="text-lg font-bold text-red-700 dark:text-red-300">£{{ number_format($invoice['amount'], 2) }}</p>
                        </div>
                        <div class="px-4 py-4 space-y-3">
                            @forelse($invoiceNotes as $note)
                                <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3">
                                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</p>
                                    <p class="mt-1 text-sm text-zinc-800 dark:text-zinc-100">{{ $note['note'] }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No follow-up note was written on this invoice this week.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                @if($account['rental_notes']->isNotEmpty())
                    <div class="border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/20">
                        <p class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-amber-900 dark:text-amber-100 border-b border-amber-200 dark:border-amber-900">Rental notes (not tied to one invoice)</p>
                        <div class="px-4 py-4 space-y-3">
                            @foreach($account['rental_notes'] as $note)
                                <div class="border border-amber-200 dark:border-amber-900 bg-white dark:bg-zinc-900 p-3">
                                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</p>
                                    <p class="mt-1 text-sm text-zinc-800 dark:text-zinc-100">{{ $note['note'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="flux-admin-panel border border-zinc-200 dark:border-zinc-800 p-6 text-sm text-zinc-500 dark:text-zinc-400">There are no unpaid rental accounts with follow-up notes in this week.</p>
    @endforelse
</div>
