<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Addresses</h2>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Address</flux:table.column>
                <flux:table.column>City</flux:table.column>
                <flux:table.column>Postcode</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Default</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($addresses as $address)
                    <flux:table.row wire:key="addr-{{ $address->id }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $address->first_name }} {{ $address->last_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $address->street_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $address->city ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $address->postcode ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $address->phone_number ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ ucfirst($address->type ?? '—') }}</flux:table.cell>
                        <flux:table.cell>
                            @if($address->is_default)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No addresses on file.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
