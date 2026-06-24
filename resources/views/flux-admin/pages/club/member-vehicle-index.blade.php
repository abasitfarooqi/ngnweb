<div>
    <x-flux-admin::data-table title="Club member vehicles" description="Quickly verify & edit the VRM / make / model / year each club member has on file.">
        <x-slot:actions>
            <x-flux-admin::export-button />
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, phone, email, VRM…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'full_name'" :direction="$sortField === 'full_name' ? $sortDirection : null" wire:click="sortBy('full_name')">Member</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Make</flux:table.column>
                <flux:table.column>Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cmv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">
                            <div>{{ $r->full_name ?: '—' }}</div>
                            <div class="text-xs text-zinc-500">{{ $r->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->phone ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono font-medium">{{ $r->vrm ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->make ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->model ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.club-member-vehicles.edit', $r) }}" wire:navigate>
                                <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit vehicle</flux:button>
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No members.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
