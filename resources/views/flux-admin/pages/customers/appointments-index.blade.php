<div>
    <x-flux-admin::data-table
        title="Customer appointments"
        description="Walk-in and service bookings raised by customers or staff."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">
                New appointment
            </flux:button>
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
                        <flux:table.cell class="text-zinc-900 dark:text-white whitespace-nowrap">{{ $a->appointment_date?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $a->customer_name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $a->registration_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            <div>{{ $a->contact_number }}</div>
                            <div class="text-xs">{{ $a->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $a->booking_reason }}</flux:table.cell>
                        <flux:table.cell>
                            <button wire:click="toggleResolved({{ $a->id }})" class="appearance-none bg-transparent p-0 m-0 border-0 cursor-pointer">
                                <x-flux-admin::status-badge :status="$a->is_resolved ? 'resolved' : 'pending'" />
                            </button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $a->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $a->id }})" wire:confirm="Delete this appointment?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No appointments found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $appointments->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[36rem]">
        <form wire:submit.prevent="saveForm" class="flex flex-col gap-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit appointment' : 'New appointment' }}</flux:heading>

            <x-flux-admin::field-group label="Appointment date" required :error="$errors->first('formData.appointment_date')">
                <flux:input type="datetime-local" wire:model="formData.appointment_date" />
            </x-flux-admin::field-group>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('formData.customer_name')">
                    <flux:input wire:model="formData.customer_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Registration" :error="$errors->first('formData.registration_number')">
                    <flux:input wire:model="formData.registration_number" class="uppercase" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Contact number" :error="$errors->first('formData.contact_number')">
                    <flux:input wire:model="formData.contact_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input type="email" wire:model="formData.email" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Booking reason" :error="$errors->first('formData.booking_reason')">
                <flux:textarea wire:model="formData.booking_reason" rows="3" />
            </x-flux-admin::field-group>

            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="formData.is_resolved" class="accent-zinc-900 dark:accent-zinc-200"> Mark as resolved
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="formData.send_email" class="accent-zinc-900 dark:accent-zinc-200"> Email the customer after saving
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <flux:button size="sm" type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button size="sm" type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
