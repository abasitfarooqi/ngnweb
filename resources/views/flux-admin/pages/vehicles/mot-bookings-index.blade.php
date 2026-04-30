<div>
    <x-flux-admin::data-table title="MOT bookings" description="Appointments scheduled for MOT testing.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/m-o-t-booking/create')" class="!rounded-none">New booking</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, customer name, phone or email…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="scheduled">Scheduled</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
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
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date_of_appointment'" :direction="$sortField === 'date_of_appointment' ? $sortDirection : null" wire:click="sortBy('date_of_appointment')">Date</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bookings as $b)
                    <flux:table.row wire:key="mot-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $b->date_of_appointment ? \Carbon\Carbon::parse($b->date_of_appointment)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $b->vehicle_registration }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $b->customer_name }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $b->customer_contact }}<br>{{ $b->customer_email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->status }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $b->is_paid" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/m-o-t-booking/'.$b->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $bookings->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
