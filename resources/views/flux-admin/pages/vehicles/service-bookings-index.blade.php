<div>
    <x-flux-admin::data-table title="Service bookings" description="Customer enquiries requesting service or repair work.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.service-bookings.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New booking</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, email, phone or VRM…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_dealt" placeholder="Dealt">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Dealt</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.enquiry_type" placeholder="Type">
                        <flux:select.option value="">Any type</flux:select.option>
                        <flux:select.option value="service">Service</flux:select.option>
                        <flux:select.option value="repair">Repair</flux:select.option>
                        <flux:select.option value="general">General</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'booking_date'" :direction="$sortField === 'booking_date' ? $sortDirection : null" wire:click="sortBy('booking_date')">Date</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Dealt</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bookings as $b)
                    <flux:table.row wire:key="sb-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y') : '—' }} {{ $b->booking_time }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-xs truncate">{{ $b->subject }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $b->fullname }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $b->phone }}<br>{{ $b->email }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $b->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->enquiry_type }}</flux:table.cell>
                        <flux:table.cell>
                            @if($b->is_dealt)
                                <flux:badge color="green" size="sm">Dealt</flux:badge>
                            @else
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="check"
                                    wire:click="markAsDealt({{ $b->id }})"
                                    class="!rounded-none"
                                >
                                    Mark dealt
                                </flux:button>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.service-bookings.edit', $b) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $b->id }})" wire:confirm="Delete this record?" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No bookings.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $bookings->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
