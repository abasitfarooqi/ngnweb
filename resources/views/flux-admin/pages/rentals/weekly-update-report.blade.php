<div class="space-y-6">
    @php
        $bands = [
            'border-l-red-700 bg-zinc-50 dark:bg-zinc-900 dark:border-l-red-500',
            'border-l-red-700 bg-red-50 dark:bg-red-950/20 dark:border-l-red-400',
            'border-l-zinc-800 bg-zinc-100 dark:bg-zinc-800 dark:border-l-zinc-400',
            'border-l-red-900 bg-orange-50 dark:bg-orange-950/20 dark:border-l-red-600',
        ];
        $heads = [
            'bg-zinc-900 text-white',
            'bg-red-800 text-white',
            'bg-zinc-800 text-white',
            'bg-red-950 text-white',
        ];
    @endphp

    <x-flux-admin::summary-header
        title="Weekly chase report"
        subtitle="One rental at a time: customer, unpaid invoices, then the notes on each invoice."
        :backUrl="route('flux-admin.rental-operations.index')"
        backLabel="Back to rentals"
    />

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 space-y-3">
        <label class="block text-sm font-medium">Reporting week</label>
        <select wire:model.live="periodKey" class="w-full max-w-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
            @foreach($periods as $period)
                <option value="{{ $period['key'] }}">{{ $period['label'] }}</option>
            @endforeach
        </select>
        <div class="flex flex-wrap gap-2">
            <flux:button size="sm" variant="primary" wire:click="downloadPdf" class="!rounded-none">Download PDF</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="emailDirector" wire:confirm="Email this snapshot to the director and copy customer service?" class="!rounded-none">Email director</flux:button>
        </div>
        <p class="text-xs text-zinc-500">Director: thiago@neguinhomotors.co.uk · CC: customerservice@neguinhomotors.co.uk</p>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 space-y-3 text-sm">
        <p class="text-zinc-500">{{ $report['start']->format('l j F Y, H:i') }} to {{ $report['end']->format('l j F Y, H:i') }}</p>
        <p>{{ $report['intro'] }}</p>
        @if($report['summary']['by_staff']->isNotEmpty())
            <table class="w-full max-w-lg border border-zinc-200 dark:border-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="text-left px-3 py-2 font-medium">Staff</th>
                        <th class="text-left px-3 py-2 font-medium">Follow-up notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['summary']['by_staff'] as $staff)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-3 py-2">{{ $staff['name'] }}</td>
                            <td class="px-3 py-2">{{ $staff['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @forelse($report['accounts'] as $account)
        <article class="border border-zinc-300 dark:border-zinc-700 border-l-8 {{ $bands[$loop->index % 4] }}">
            <header class="px-4 py-3 {{ $heads[$loop->index % 4] }}">
                <h2 class="text-base font-semibold">
                    <a href="{{ route('flux-admin.rentals.show', $account['booking_id']) }}" class="underline">Rental #{{ $account['booking_id'] }} · {{ $account['customer'] }}</a>
                </h2>
                <p class="mt-1 text-sm">
                    Customer #{{ $account['customer_id'] ?: '—' }}
                    · {{ $account['phone'] }}
                    · {{ $account['email'] }}
                    · Vehicle {{ $account['registration'] }}
                    @if($account['weekly_rent'])
                        · Weekly rent £{{ number_format((float) $account['weekly_rent'], 2) }}
                    @endif
                    · Still unpaid £{{ number_format($account['outstanding'], 2) }}
                </p>
            </header>
            <div class="p-4 space-y-3 text-sm">
                <p>{{ $account['story'] }}</p>

                @foreach($account['invoices'] as $invoice)
                    <div class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900">
                        <p class="px-3 py-2 font-semibold border-b border-zinc-200 dark:border-zinc-700">
                            Invoice #{{ $invoice['id'] }} · {{ $invoice['date'] }} · £{{ number_format($invoice['amount'], 2) }} · Unpaid
                        </p>
                        <div class="px-3 py-3 space-y-3">
                            @forelse($invoice['notes'] as $note)
                                <div>
                                    <p class="text-xs text-zinc-500">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</p>
                                    <p>{{ $note['note'] }}</p>
                                </div>
                            @empty
                                <p class="text-zinc-500">No follow-up note was written on this invoice this week.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                @if($account['rental_notes']->isNotEmpty())
                    <div class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900">
                        <p class="px-3 py-2 font-semibold border-b border-zinc-200 dark:border-zinc-700">Rental notes (not tied to one invoice)</p>
                        <div class="px-3 py-3 space-y-3">
                            @foreach($account['rental_notes'] as $note)
                                <div>
                                    <p class="text-xs text-zinc-500">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</p>
                                    <p>{{ $note['note'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <p class="text-sm text-zinc-500">There are no unpaid rental accounts with follow-up notes in this week.</p>
    @endforelse
</div>
