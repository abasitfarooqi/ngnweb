<div>
    <x-flux-admin::data-table title="Service bookings" description="Customer enquiries requesting service or repair work.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/service-booking/create')" class="!rounded-none">New booking</flux:button>
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
                        <flux:table.cell><flux:switch :checked="(bool) $b->is_dealt" wire:click="toggleDealt({{ $b->id }})" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/service-booking/'.$b->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
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
