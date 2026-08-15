<div>
    <x-flux-admin::data-table title="Rental terminate links" description="Passcode URLs letting customers confirm rental termination.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.rental-terminate-links.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New link</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search booking ID, link ID, passcode, reg, name, email or phone…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>Passcode</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Customer link</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    @php
                        $bike = $r->bookings?->rentingBookingItems?->first()?->motorbike;
                        $isSigned = (bool) $r->signed_at;
                        $isExpired = ! $isSigned && $r->expire_at && \Carbon\Carbon::parse($r->expire_at)->lte(now());
                    @endphp
                    <flux:table.row wire:key="rta-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->booking_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customers ? $r->customers->first_name.' '.$r->customers->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="font-mono text-xs text-zinc-900 dark:text-white">{{ $bike?->reg_no ?: '—' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ trim(($bike?->make ?? '').' '.($bike?->model ?? '')) ?: '—' }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->passcode }}</flux:table.cell>
                        <flux:table.cell>
                            @if($isSigned)
                                <flux:badge color="emerald" size="sm">Signed</flux:badge>
                            @elseif($isExpired)
                                <flux:badge color="red" size="sm">Expired</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">Active</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            <a href="{{ $r->customerBookingUrl() }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400 break-all">
                                {{ $r->customerBookingUrl() }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            <div>{{ $r->expire_at ? \Carbon\Carbon::parse($r->expire_at)->format('d M Y H:i') : '—' }}</div>
                            @if($r->signed_at)
                                <div class="text-xs text-zinc-500 dark:text-zinc-500">Signed {{ \Carbon\Carbon::parse($r->signed_at)->format('d M Y H:i') }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.rental-terminate-links.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this link?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
