<div>
    <x-flux-admin::data-table title="Contract links" description="Passcode URLs allowing customers to sign finance contracts.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.contract-access.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New link</flux:button></a>
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
                <flux:table.column>Contract links</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    @php
                        $accessLinks = \App\Services\FinanceContractLinkResolver::linksForContractAccess($r);
                    @endphp
                    <flux:table.row wire:key="ca-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">#{{ $r->application_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->passcode }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($accessLinks)
                                <div class="space-y-2 min-w-[18rem]">
                                    @foreach($accessLinks as $link)
                                        <div>
                                            <p class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">{{ $link['label'] }}</p>
                                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $link['url'] }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-zinc-400">No matching latest contract</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.contract-access.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $r->id }})" wire:confirm="Delete this contract link?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
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
