<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">All bookings</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Full booking history — filter by customer, vehicle, state or date range.</p>
        </div>
        <flux:button variant="ghost" wire:click="resetFilters">Reset filters</flux:button>
    </div>

    <div class="flux-admin-toolbar mb-6 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <flux:input
                class="lg:col-span-2"
                wire:model.live.debounce.300ms="search"
                placeholder="Search booking ID, customer, registration…"
                variant="filled"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filters.status" placeholder="Any status">
                <flux:select.option value="">Any status</flux:select.option>
                <flux:select.option value="ONGOING">Ongoing</flux:select.option>
                <flux:select.option value="ENDED">Ended</flux:select.option>
                <flux:select.option value="NA">N/A</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filters.state" placeholder="Any state">
                <flux:select.option value="">Any state</flux:select.option>
                @foreach ($states as $state)
                    <flux:select.option value="{{ $state }}">{{ $state }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filters.customer_id" placeholder="Any customer">
                <flux:select.option value="">Any customer</flux:select.option>
                @foreach ($customers as $c)
                    <flux:select.option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filters.motorbike_id" placeholder="Any motorbike">
                <flux:select.option value="">Any motorbike</flux:select.option>
                @foreach ($motorbikes as $m)
                    <flux:select.option value="{{ $m->id }}">{{ $m->reg_no ?: ('#'.$m->id) }} — {{ $m->make }} {{ $m->model }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="date" wire:model.live="filters.from" label="From" />
            <flux:input type="date" wire:model.live="filters.to" label="To" />
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
                        <flux:table.column sortable :sorted="$sortField === 'weekly_rent'" :direction="$sortDirection" wire:click="sortBy('weekly_rent')">Weekly</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'start_date'" :direction="$sortDirection" wire:click="sortBy('start_date')">Start</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'end_date'" :direction="$sortDirection" wire:click="sortBy('end_date')">End</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($rows as $r)
                            <flux:table.row wire:key="ab-{{ $r->booking_item_id }}">
                                <flux:table.cell class="font-medium">#{{ $r->booking_id }}</flux:table.cell>
                                <flux:table.cell><flux:badge size="sm" color="zinc">{{ $r->booking_state ?: '—' }}</flux:badge></flux:table.cell>
                                <flux:table.cell class="font-mono">{{ $r->reg_no ?: '—' }}<span class="block text-xs text-zinc-500">{{ $r->make }} {{ $r->model }}</span></flux:table.cell>
                                <flux:table.cell>{{ $r->first_name }} {{ $r->last_name }}<span class="block text-xs text-zinc-500">{{ $r->phone }}</span></flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) $r->weekly_rent, 2) }}</flux:table.cell>
                                <flux:table.cell>{{ $r->item_start_date ? \Carbon\Carbon::parse($r->item_start_date)->format('d M Y') : '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($r->item_end_date && $r->item_end_date !== 'N/A' && $r->item_end_date !== '')
                                        {{ \Carbon\Carbon::parse($r->item_end_date)->format('d M Y') }}
                                    @else
                                        <flux:badge size="sm" color="emerald">Ongoing</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="sm" variant="ghost" icon="eye" href="{{ route('flux-admin.rentals.show', $r->booking_id) }}" wire:navigate>Open</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="py-8 text-center text-sm text-zinc-500">No bookings match the filters.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
