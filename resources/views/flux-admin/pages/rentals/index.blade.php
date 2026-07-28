<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $pageTitle }}</h1>
            <x-flux-admin::list-count :total="$rows->total()" :label="$listCountLabel" />
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('flux-admin.new-booking.index') }}" wire:navigate variant="primary" icon="plus" class="!rounded-none">New booking</flux:button>
        </div>
    </div>

    @if($stats)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-flux-admin::stat-card
                label="Active rentals"
                :value="number_format($stats['active_rentals'])"
                icon="key"
                colour="green"
            />
            <x-flux-admin::stat-card
                label="Weekly revenue"
                :value="'£' . number_format($stats['weekly_revenue'], 2)"
                icon="currency-pound"
                colour="blue"
            />
            <x-flux-admin::stat-card
                label="Due payments"
                :value="number_format($stats['due_payments'])"
                icon="clock"
                colour="amber"
            />
            <x-flux-admin::stat-card
                label="Unpaid invoices"
                :value="'£' . number_format($stats['unpaid_invoices'], 2)"
                icon="exclamation-triangle"
                colour="red"
            />
        </div>
    @endif

    <div class="flux-admin-toolbar mb-6 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search booking ID, customer, registration, make, model…"
                    variant="filled"
                    icon="magnifying-glass"
                />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                @if($scope === 'active')
                    <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-44 lg:flex-none">
                        <flux:select wire:model.live="status" placeholder="Payment status">
                            <flux:select.option value="all">All payment statuses</flux:select.option>
                            <flux:select.option value="active">No amount due</flux:select.option>
                            <flux:select.option value="payment_due">Payment due</flux:select.option>
                        </flux:select>
                    </div>
                @endif
                <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="bookingStateFilter" placeholder="Booking state">
                        <flux:select.option value="">All states</flux:select.option>
                        @foreach ($states as $state)
                            <flux:select.option value="{{ $state }}">{{ $state }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:input type="date" wire:model.live="startDateFrom" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:input type="date" wire:model.live="startDateTo" />
                </div>
                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-32">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="10">10 per page</flux:select.option>
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[72rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'booking_id'" :direction="$sortDirection" wire:click="sortBy('booking_id')">Booking</flux:table.column>
                        <flux:table.column>State</flux:table.column>
                        <flux:table.column>Reg</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'customer'" :direction="$sortDirection" wire:click="sortBy('customer')">Customer</flux:table.column>
                        <flux:table.column>Contact</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'weekly_rent'" :direction="$sortDirection" wire:click="sortBy('weekly_rent')">Weekly rent</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'start_date'" :direction="$sortDirection" wire:click="sortBy('start_date')">Start</flux:table.column>
                        @if($scope !== 'active')
                            <flux:table.column sortable :sorted="$sortField === 'end_date'" :direction="$sortDirection" wire:click="sortBy('end_date')">End</flux:table.column>
                        @else
                            <flux:table.column sortable :sorted="$sortField === 'due_date'" :direction="$sortDirection" wire:click="sortBy('due_date')">Due</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'outstanding'" :direction="$sortDirection" wire:click="sortBy('outstanding')">Outstanding</flux:table.column>
                        @endif
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($rows as $row)
                            <flux:table.row wire:key="rental-{{ $row->booking_item_id }}">
                                <flux:table.cell class="font-medium">
                                    #{{ $row->booking_id }}
                                    <span class="block text-xs text-zinc-500">Item {{ $row->booking_item_id }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" :color="str_contains(strtolower((string) $row->booking_state), 'completed') ? 'emerald' : (str_contains(strtolower((string) $row->booking_state), 'await') ? 'amber' : 'zinc')">
                                        {{ $row->booking_state ?: '—' }}
                                    </flux:badge>
                                    @if(! $row->booking_is_posted)
                                        <span class="block text-xs text-amber-600 dark:text-amber-400 mt-0.5">Unposted</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="font-mono">
                                    {{ $row->reg_no ?: '—' }}
                                    <span class="block text-xs text-zinc-500 font-sans">{{ $row->make }} {{ $row->model }}</span>
                                </flux:table.cell>
                                <flux:table.cell>{{ $row->first_name }} {{ $row->last_name }}</flux:table.cell>
                                <flux:table.cell class="text-xs">
                                    <div class="truncate max-w-[18ch]">{{ $row->email }}</div>
                                    <div class="text-zinc-500">{{ $row->phone }}</div>
                                </flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) $row->weekly_rent, 2) }}</flux:table.cell>
                                <flux:table.cell>{{ $row->item_start_date ? \Carbon\Carbon::parse($row->item_start_date)->format('d M Y') : '—' }}</flux:table.cell>
                                @if($scope !== 'active')
                                    <flux:table.cell>{{ $row->item_end_date ? \Carbon\Carbon::parse($row->item_end_date)->format('d M Y') : '—' }}</flux:table.cell>
                                @else
                                    <flux:table.cell>{{ $row->item_due_date ? \Carbon\Carbon::parse($row->item_due_date)->format('d M Y') : '—' }}</flux:table.cell>
                                    <flux:table.cell>
                                        @if($row->outstanding_amount > 0)
                                            <span class="text-red-600 dark:text-red-400 font-semibold">£{{ number_format($row->outstanding_amount, 2) }}</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400">£0.00</span>
                                        @endif
                                    </flux:table.cell>
                                @endif
                                <flux:table.cell>
                                    <flux:button size="sm" variant="ghost" icon="eye" href="{{ route('flux-admin.rentals.show', $row->booking_id) }}" wire:navigate class="!rounded-none">Open</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                    No bookings found.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
