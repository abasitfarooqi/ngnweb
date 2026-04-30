<div>
    <x-flux-admin::data-table title="Vehicle history" description="Unified timeline of every VRM across databases (bookings, sales, claims, recoveries).">
        <x-slot:actions>
            <x-flux-admin::export-button />
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, person or database…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.database" placeholder="Database">
                        <flux:select.option value="">Any database</flux:select.option>
                        <flux:select.option value="renting_bookings">Renting bookings</flux:select.option>
                        <flux:select.option value="finance_applications">Finance applications</flux:select.option>
                        <flux:select.option value="motorbikes_sales">Motorbike sales</flux:select.option>
                        <flux:select.option value="claim_motorbikes">Claims</flux:select.option>
                        <flux:select.option value="recovered_motorbikes">Recovered</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'START_DATE'" :direction="$sortField === 'START_DATE' ? $sortDirection : null" wire:click="sortBy('START_DATE')">Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Database</flux:table.column>
                <flux:table.column>Doc ID</flux:table.column>
                <flux:table.column>Person</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="mrv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->START_DATE ? \Carbon\Carbon::parse($r->START_DATE)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->END_DATE ? \Carbon\Carbon::parse($r->END_DATE)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono font-medium text-zinc-900 dark:text-white">{{ $r->VRM }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->DATABASE }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->DOC_ID }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->PERSON }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
