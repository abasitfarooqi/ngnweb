<div>
    <x-flux-admin::data-table title="Motorbike claims" description="Third-party claims for recovered/abandoned motorbikes.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/claim-motorbike/create')" class="!rounded-none">New claim</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search claimant or registration…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_received" placeholder="Received">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Received</flux:select.option>
                        <flux:select.option value="0">Not received</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_returned" placeholder="Returned">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Returned</flux:select.option>
                        <flux:select.option value="0">Still held</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'case_date'" :direction="$sortField === 'case_date' ? $sortDirection : null" wire:click="sortBy('case_date')">Case date</flux:table.column>
                <flux:table.column>Claimant</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Received</flux:table.column>
                <flux:table.column>Returned</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->case_date ? \Carbon\Carbon::parse($r->case_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->fullname }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $r->phone }}<br>{{ $r->email }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_received" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_returned" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/claim-motorbike/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
