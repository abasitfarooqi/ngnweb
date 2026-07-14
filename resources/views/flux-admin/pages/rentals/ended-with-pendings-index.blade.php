<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Ended with pendings</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ended bookings closed “proceeded anyway” that still have rental, additional or PCN balances.</p>
        </div>
        <a href="{{ route('flux-admin.inactive-bookings.index') }}">
            <flux:button size="sm" variant="ghost" class="!rounded-none">Inactive bookings</flux:button>
        </a>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search booking, customer or reg…" icon="magnifying-glass" />
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Ended</flux:table.column>
                <flux:table.column>Rental left</flux:table.column>
                <flux:table.column>Additional</flux:table.column>
                <flux:table.column>PCN left</flux:table.column>
                <flux:table.column>Proceeded by</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $row)
                    <flux:table.row wire:key="ewp-{{ $row->booking_id }}-{{ $row->booking_item_id }}">
                        <flux:table.cell>#{{ $row->booking_id }}</flux:table.cell>
                        <flux:table.cell class="text-xs">
                            <div class="text-zinc-900 dark:text-white">{{ $row->first_name }} {{ $row->last_name }}</div>
                            <div class="text-zinc-500">{{ $row->phone }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $row->reg_no }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap text-xs">{{ $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format((float) $row->rental_left, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format((float) $row->additional_left, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format((float) $row->pcn_left, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $row->proceeded_by }}</flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.rentals.show', $row->booking_id) }}" wire:navigate>
                                <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none">Open</flux:button>
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="py-8 text-center text-zinc-500">No ended bookings with proceed-anyway pendings.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">{{ $rows->links() }}</div>
    </div>
</div>
