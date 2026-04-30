<div class="space-y-6">
    <div>
        <flux:heading size="xl">Active rentals</flux:heading>
        <flux:text class="mt-1">Live overview of bookings with open rental items and outstanding invoices.</flux:text>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <x-flux-admin::stat-card label="Active rentals" :value="$stats['active_rentals']" icon="truck" />
        <x-flux-admin::stat-card label="Weekly revenue" :value="'£'.number_format($stats['weekly_revenue'], 2)" icon="banknotes" />
        <x-flux-admin::stat-card label="Due payments" :value="$stats['due_payments']" icon="exclamation-triangle" />
        <x-flux-admin::stat-card label="Unpaid total" :value="'£'.number_format($stats['unpaid_invoices'], 2)" icon="credit-card" />
        <x-flux-admin::stat-card label="Total deposits" :value="'£'.number_format($stats['total_deposits'], 2)" icon="lock-closed" />
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>Weekly</flux:table.column>
                <flux:table.column>Unpaid invoices</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($activeBookings as $b)
                    @php
                        $items = $b->rentingBookingItems->whereNull('end_date');
                        $unpaid = $b->bookingInvoices->count();
                        $unpaidTotal = $b->bookingInvoices->sum('amount');
                    @endphp
                    <flux:table.row wire:key="arb-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white font-mono text-sm">#{{ $b->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $b->customer?->first_name }} {{ $b->customer?->last_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 text-xs">
                            @foreach($items as $item)
                                <div>{{ $item->motorbike?->reg_no ?? '—' }}</div>
                            @endforeach
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $items->sum('weekly_rent'), 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-zinc-900 dark:text-white">{{ $unpaid }}</span>
                            <span class="ml-2 text-xs text-red-600 dark:text-red-400">£{{ number_format((float) $unpaidTotal, 2) }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none" href="{{ route('flux-admin.rentals.show', $b->id) }}" wire:navigate>View</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No active rentals.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
