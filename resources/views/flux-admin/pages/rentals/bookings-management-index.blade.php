<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $pageTitle }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $pageDescription }}</p>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('flux-admin.new-booking.index') }}" wire:navigate variant="primary" icon="plus">New booking</flux:button>
        </div>
    </div>

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
                <div class="min-w-0 w-full sm:min-w-[14rem] sm:flex-1 lg:w-52 lg:flex-none">
                    <flux:select wire:model.live="filters.state" placeholder="All states">
                        <flux:select.option value="">All states</flux:select.option>
                        @foreach ($states as $state)
                            <flux:select.option value="{{ $state }}">{{ $state }}</flux:select.option>
                        @endforeach
                    </flux:select>
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
            <div class="min-w-[64rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'booking_id'" :direction="$sortDirection" wire:click="sortBy('booking_id')">Booking</flux:table.column>
                        <flux:table.column>State</flux:table.column>
                        <flux:table.column>Reg</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'customer'" :direction="$sortDirection" wire:click="sortBy('customer')">Customer</flux:table.column>
                        <flux:table.column>Contact</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'weekly_rent'" :direction="$sortDirection" wire:click="sortBy('weekly_rent')">Weekly rent</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'start_date'" :direction="$sortDirection" wire:click="sortBy('start_date')">Start</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'end_date'" :direction="$sortDirection" wire:click="sortBy('end_date')">End</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($rows as $r)
                            <flux:table.row wire:key="bk-{{ $r->booking_item_id }}">
                                <flux:table.cell class="font-medium">#{{ $r->booking_id }} <span class="block text-xs text-zinc-500">Item {{ $r->booking_item_id }}</span></flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" :color="str_contains(strtolower((string) $r->booking_state), 'completed') ? 'emerald' : (str_contains(strtolower((string) $r->booking_state), 'await') ? 'amber' : 'zinc')">
                                        {{ $r->booking_state ?: '—' }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="font-mono">{{ $r->reg_no ?: '—' }}<span class="block text-xs text-zinc-500">{{ $r->make }} {{ $r->model }}</span></flux:table.cell>
                                <flux:table.cell>{{ $r->first_name }} {{ $r->last_name }}</flux:table.cell>
                                <flux:table.cell class="text-xs">
                                    <div class="truncate max-w-[18ch]">{{ $r->email }}</div>
                                    <div class="text-zinc-500">{{ $r->phone }}</div>
                                </flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) $r->weekly_rent, 2) }}</flux:table.cell>
                                <flux:table.cell>{{ $r->item_start_date ? \Carbon\Carbon::parse($r->item_start_date)->format('d M Y') : '—' }}</flux:table.cell>
                                <flux:table.cell>{{ $r->item_end_date ? \Carbon\Carbon::parse($r->item_end_date)->format('d M Y') : '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="sm" variant="ghost" icon="eye" href="{{ route('flux-admin.rentals.show', $r->booking_id) }}" wire:navigate>Open</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="9" class="py-8 text-center text-sm text-zinc-500">No bookings found.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
