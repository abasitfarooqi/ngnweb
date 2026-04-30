<div>
    <x-flux-admin::data-table title="Motorbike repairs" description="Workshop repair log with PDF export for each entry.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/motorbike-repair/create')" class="!rounded-none">New repair</flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer, phone, email or registration…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_repaired" placeholder="Repaired">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Yes</flux:select.option>
                        <flux:select.option value="0">No</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_returned" placeholder="Returned">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Yes</flux:select.option>
                        <flux:select.option value="0">No</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'arrival_date'" :direction="$sortField === 'arrival_date' ? $sortDirection : null" wire:click="sortBy('arrival_date')">Arrival</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Repaired</flux:table.column>
                <flux:table.column>Returned</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($repairs as $r)
                    <flux:table.row wire:key="repair-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->arrival_date ? \Carbon\Carbon::parse($r->arrival_date)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->fullname }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->phone }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_repaired" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_returned" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="generatePdf({{ $r->id }})" icon="document-arrow-down" class="!rounded-none">PDF</flux:button>
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/motorbike-repair/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No repairs on file.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $repairs->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
