<div>
    <x-flux-admin::data-table title="Contract links" description="Passcode URLs allowing customers to sign finance contracts.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/contract-access/create')" class="!rounded-none">New link</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search passcode, contract ID or customer…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Contract ID</flux:table.column>
                <flux:table.column>Passcode</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="ca-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">#{{ $r->application_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->passcode }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" :href="'https://neguinhomotors.co.uk/sale-ins-latest/'.$r->customer_id.'/'.$r->passcode" target="_blank" icon="link" class="!rounded-none">Link</flux:button>
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/contract-access/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No links.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
