<div>
    <x-flux-admin::data-table title="Rental agreement links" description="Passcode URLs for customers to sign rental agreements and loyalty scheme.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/agreement-access/create')" class="!rounded-none">New link</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search passcode, booking ID or customer…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Rental agreement</flux:table.column>
                <flux:table.column>Loyalty scheme</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="aa-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">#{{ $r->booking_id }}</flux:table.cell>
                        <flux:table.cell class="text-xs"><a href="{{ url('/rental-agreement/'.$r->customer_id.'/'.$r->passcode) }}" target="_blank" class="text-blue-600 hover:underline">Open</a></flux:table.cell>
                        <flux:table.cell class="text-xs"><a href="{{ url('/loyalty-scheme/'.$r->customer_id.'/'.$r->passcode) }}" target="_blank" class="text-blue-600 hover:underline">Open</a></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/agreement-access/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No links.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
