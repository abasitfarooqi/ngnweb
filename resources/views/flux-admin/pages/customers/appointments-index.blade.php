<div>
    <x-flux-admin::data-table
        title="Customer appointments"
        description="Walk-in and service bookings raised by customers or staff."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.customer-appointments.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">
                    New appointment
                </flux:button>
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, email, phone or plate…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.resolved" placeholder="Resolved">
                        <flux:select.option value="">All</flux:select.option>
                        <flux:select.option value="1">Resolved</flux:select.option>
                        <flux:select.option value="0">Outstanding</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="date" wire:model.live="filters.from" placeholder="From" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="date" wire:model.live="filters.to" placeholder="To" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'appointment_date'" :direction="$sortField === 'appointment_date' ? $sortDirection : null" wire:click="sortBy('appointment_date')">When</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($appointments as $a)
                    <flux:table.row wire:key="appt-{{ $a->id }}">
                        <flux:table.cell class="whitespace-nowrap text-zinc-900 dark:text-white">{{ $a->appointment_date?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $a->customer_name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $a->registration_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            <div>{{ $a->contact_number }}</div>
                            <div class="text-xs">{{ $a->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-sm truncate text-zinc-600 dark:text-zinc-400">{{ $a->booking_reason }}</flux:table.cell>
                        <flux:table.cell>
                            <button wire:click="toggleResolved({{ $a->id }})" class="m-0 cursor-pointer appearance-none border-0 bg-transparent p-0">
                                <x-flux-admin::status-badge :status="$a->is_resolved ? 'resolved' : 'pending'" />
                            </button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.customer-appointments.edit', $a) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $a->id }})" wire:confirm="Delete this appointment?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="py-8 text-center text-zinc-500 dark:text-zinc-400">No appointments found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $appointments->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
