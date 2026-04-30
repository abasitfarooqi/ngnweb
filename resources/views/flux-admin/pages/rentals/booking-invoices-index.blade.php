<div>
    <x-flux-admin::data-table title="Booking invoices" description="Weekly rental invoices raised against bookings.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/booking-invoice/create')" class="!rounded-none">New invoice</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search booking, customer or state…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_paid" placeholder="Paid">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Paid</flux:select.option>
                        <flux:select.option value="0">Unpaid</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_posted" placeholder="Posted">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Posted</flux:select.option>
                        <flux:select.option value="0">Draft</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'invoice_date'" :direction="$sortField === 'invoice_date' ? $sortDirection : null" wire:click="sortBy('invoice_date')">Date</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Deposit</flux:table.column>
                <flux:table.column>State</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($invoices as $i)
                    <flux:table.row wire:key="bi-{{ $i->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $i->id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">#{{ $i->booking_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $i->booking?->customer ? $i->booking->customer->first_name.' '.$i->booking->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $i->invoice_date ? \Carbon\Carbon::parse($i->invoice_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $i->amount, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $i->deposit, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $i->state }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $i->is_paid" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/booking-invoice/'.$i->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No invoices.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $invoices->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
