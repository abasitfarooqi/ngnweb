<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-x-auto">
        @if($logs->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>Cost</flux:table.column>
                    <flux:table.column>Booking ID</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Note</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($logs as $log)
                        <flux:table.row wire:key="maint-{{ $log->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $log->serviced_at ? \Carbon\Carbon::parse($log->serviced_at)->format('d M Y') : '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="max-w-xs truncate">{{ $log->description ?: '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $log->cost ? '£' . number_format($log->cost, 2) : '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $log->booking_id ?: '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $log->user?->first_name ?? '—' }}</flux:table.cell>
                            <flux:table.cell class="max-w-xs truncate">{{ $log->note ?: '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No maintenance records found.
            </div>
        @endif
    </div>
</div>
