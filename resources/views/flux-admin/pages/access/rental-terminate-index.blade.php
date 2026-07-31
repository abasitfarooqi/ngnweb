<div>
    <x-flux-admin::data-table title="Rental terminate links" description="Passcode URLs letting customers confirm rental termination.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.rental-terminate-links.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New link</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search passcode or booking ID…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Passcode</flux:table.column>
                <flux:table.column>Customer link</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="rta-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->booking_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customers ? $r->customers->first_name.' '.$r->customers->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->passcode }}</flux:table.cell>
                        <flux:table.cell class="text-xs">
                            <a href="{{ $r->customerBookingUrl() }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400 break-all">
                                {{ $r->customerBookingUrl() }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->expire_at ? \Carbon\Carbon::parse($r->expire_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.rental-terminate-links.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this link?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
