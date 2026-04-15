<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Bookings</h2>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Start Date</flux:table.column>
                <flux:table.column>Due Date</flux:table.column>
                <flux:table.column>State</flux:table.column>
                <flux:table.column>Deposit</flux:table.column>
                <flux:table.column>Items</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($bookings as $booking)
                    <flux:table.row wire:key="booking-{{ $booking->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.rentals.show', $booking) }}" class="font-medium text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                #{{ $booking->id }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $booking->due_date ? \Carbon\Carbon::parse($booking->due_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $stateColour = match($booking->state) {
                                    'active' => 'green',
                                    'completed', 'returned' => 'blue',
                                    'cancelled' => 'red',
                                    'overdue' => 'amber',
                                    default => 'zinc',
                                };
                            @endphp
                            <flux:badge :color="$stateColour" size="sm">{{ ucfirst($booking->state ?? '—') }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">&pound;{{ number_format($booking->deposit ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $booking->rentingBookingItems->count() }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No bookings found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
