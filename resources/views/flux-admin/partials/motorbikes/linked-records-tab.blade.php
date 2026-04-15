<div class="space-y-6">
    {{-- Rental Booking Items --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Rental Bookings</h2>
        </div>
        @if($bookingItems->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Booking ID</flux:table.column>
                    <flux:table.column>Weekly Rent</flux:table.column>
                    <flux:table.column>Start Date</flux:table.column>
                    <flux:table.column>Due Date</flux:table.column>
                    <flux:table.column>End Date</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($bookingItems as $item)
                        <flux:table.row wire:key="bi-{{ $item->id }}">
                            <flux:table.cell>
                                @if($item->booking_id)
                                    <a href="{{ route('flux-admin.rentals.show', $item->booking_id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                        #{{ $item->booking_id }}
                                    </a>
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>£{{ number_format($item->weekly_rent, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $item->start_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $item->due_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $item->end_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$item->is_posted ? 'green' : 'zinc'" size="sm">
                                    {{ $item->is_posted ? 'Posted' : 'Draft' }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-6 text-center text-sm text-zinc-500 dark:text-zinc-400">No rental bookings linked.</div>
        @endif
    </div>

    {{-- Finance Application Items --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Finance Applications</h2>
        </div>
        @if($applicationItems->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Application ID</flux:table.column>
                    <flux:table.column>Weekly Instalment</flux:table.column>
                    <flux:table.column>Start Date</flux:table.column>
                    <flux:table.column>Due Date</flux:table.column>
                    <flux:table.column>End Date</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($applicationItems as $appItem)
                        <flux:table.row wire:key="ai-{{ $appItem->id }}">
                            <flux:table.cell>
                                @if($appItem->application_id)
                                    <a href="{{ route('flux-admin.finance.show', $appItem->application_id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                        #{{ $appItem->application_id }}
                                    </a>
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>£{{ number_format($appItem->weekly_instalment, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ $appItem->start_date ? \Carbon\Carbon::parse($appItem->start_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $appItem->due_date ? \Carbon\Carbon::parse($appItem->due_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $appItem->end_date ? \Carbon\Carbon::parse($appItem->end_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$appItem->is_posted ? 'green' : 'zinc'" size="sm">
                                    {{ $appItem->is_posted ? 'Posted' : 'Draft' }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-6 text-center text-sm text-zinc-500 dark:text-zinc-400">No finance applications linked.</div>
        @endif
    </div>

    {{-- PCN Cases --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">PCN Cases</h2>
        </div>
        @if($pcnCases->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>PCN Number</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Full Amount</flux:table.column>
                    <flux:table.column>Reduced Amount</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($pcnCases as $pcn)
                        <flux:table.row wire:key="pcn-{{ $pcn->id }}">
                            <flux:table.cell>
                                <a href="{{ route('flux-admin.pcn.show', $pcn->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                    {{ $pcn->pcn_number }}
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>{{ $pcn->date_of_contravention?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $pcn->full_amount ? '£' . number_format($pcn->full_amount, 2) : '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $pcn->reduced_amount ? '£' . number_format($pcn->reduced_amount, 2) : '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$pcn->isClosed ? 'zinc' : 'red'" size="sm">
                                    {{ $pcn->isClosed ? 'Closed' : 'Open' }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-6 text-center text-sm text-zinc-500 dark:text-zinc-400">No PCN cases linked.</div>
        @endif
    </div>
</div>
