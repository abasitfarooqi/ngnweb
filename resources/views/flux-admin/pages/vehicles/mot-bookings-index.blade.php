<div>
    <x-flux-admin::data-table title="MOT bookings" description="MOT appointment slots — booked, available, completed or cancelled.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.mot-bookings.calendar') }}" wire:navigate>
                <flux:button size="sm" variant="ghost" icon="calendar-days" class="!rounded-none">Calendar</flux:button>
            </a>
            <a href="{{ route('flux-admin.mot-bookings.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New booking</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, customer, title or payment link…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="booked">Booked</flux:select.option>
                        <flux:select.option value="available">Available</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.is_paid" placeholder="Paid">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Paid</flux:select.option>
                        <flux:select.option value="0">Unpaid</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
            @if($branch)
                <!-- <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Showing Catford MOT bookings only.</p> -->
            @endif
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'start'" :direction="$sortField === 'start' ? $sortDirection : null" wire:click="sortBy('start')">Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column>Payment link</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bookings as $b)
                    <flux:table.row wire:key="mot-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $b->start ? \Carbon\Carbon::parse($b->start)->format('d M Y').' '.\App\Support\BookingSchedule::formatTimeAmPm(\Carbon\Carbon::parse($b->start)->format('H:i')) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $b->end ? \Carbon\Carbon::parse($b->end)->format('d M Y').' '.\App\Support\BookingSchedule::formatTimeAmPm(\Carbon\Carbon::parse($b->end)->format('H:i')) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-700 dark:text-zinc-300 max-w-[14rem] truncate" title="{{ $b->title }}">{{ $b->title ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $b->vehicle_registration }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">
                            <div class="text-zinc-900 dark:text-white">{{ $b->customer_name }}</div>
                            <div>{{ $b->customer_contact }}</div>
                            <div>{{ $b->customer_email }}</div>
                        </flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$b->status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $b->is_paid" /></flux:table.cell>
                        <flux:table.cell class="text-xs max-w-[10rem] truncate">
                            @if($b->payment_link)
                                <a href="{{ $b->payment_link }}" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 underline">Link</a>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.mot-bookings.edit', $b->id) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $b->id }})" wire:confirm="Delete this record?" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $bookings->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
