<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Rentals</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Active rental bookings and payment overview.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-flux-admin::stat-card
            label="Active Rentals"
            :value="number_format($stats['active_rentals'])"
            icon="key"
            colour="green"
        />
        <x-flux-admin::stat-card
            label="Weekly Revenue"
            :value="'£' . number_format($stats['weekly_revenue'], 2)"
            icon="currency-pound"
            colour="blue"
        />
        <x-flux-admin::stat-card
            label="Due Payments"
            :value="number_format($stats['due_payments'])"
            icon="clock"
            colour="amber"
        />
        <x-flux-admin::stat-card
            label="Unpaid Invoices"
            :value="'£' . number_format($stats['unpaid_invoices'], 2)"
            icon="exclamation-triangle"
            colour="red"
        />
    </div>

    {{-- Filters --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 mb-6">
        <div class="p-4 flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Search by booking ID, customer, reg no, make, model..."
                    size="sm"
                />
            </div>
            <flux:select wire:model.live="status" size="sm" class="w-full sm:w-48">
                <option value="all">All Statuses</option>
                <option value="active">Active (No Due)</option>
                <option value="payment_due">Payment Due</option>
            </flux:select>
            <flux:select wire:model.live="perPage" size="sm" class="w-full sm:w-28">
                <option value="10">10 / page</option>
                <option value="20">20 / page</option>
                <option value="50">50 / page</option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'booking_id'" :direction="$sortDirection" wire:click="sortBy('booking_id')">Booking ID</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'weekly_rent'" :direction="$sortDirection" wire:click="sortBy('weekly_rent')">Weekly Rent</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'start_date'" :direction="$sortDirection" wire:click="sortBy('start_date')">Start Date</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'due_date'" :direction="$sortDirection" wire:click="sortBy('due_date')">Due Date</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'outstanding'" :direction="$sortDirection" wire:click="sortBy('outstanding')">Outstanding</flux:table.column>
                <flux:table.column>State</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($rows as $row)
                    <flux:table.row
                        wire:key="rental-{{ $row->booking_item_id }}"
                        class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50"
                        wire:click="$navigate('{{ route('flux-admin.rentals.show', $row->booking_id) }}')"
                    >
                        <flux:table.cell class="font-medium">#{{ $row->booking_id }}</flux:table.cell>
                        <flux:table.cell>{{ $row->first_name }} {{ $row->last_name }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="font-medium">{{ $row->reg_no }}</span>
                            <span class="text-zinc-500 dark:text-zinc-400 text-xs block">{{ $row->make }} {{ $row->model }}</span>
                        </flux:table.cell>
                        <flux:table.cell>£{{ number_format($row->weekly_rent, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($row->item_start_date)->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $row->item_due_date ? \Carbon\Carbon::parse($row->item_due_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->outstanding_amount > 0)
                                <span class="text-red-600 dark:text-red-400 font-semibold">£{{ number_format($row->outstanding_amount, 2) }}</span>
                            @else
                                <span class="text-green-600 dark:text-green-400">£0.00</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$row->booking_state === 'active' ? 'green' : 'zinc'" size="sm">
                                {{ ucfirst($row->booking_state ?? 'N/A') }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No rental bookings found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div class="mt-4">
        {{ $rows->links() }}
    </div>
</div>
