<div>
    <x-flux-admin::data-table
        title="New motorbikes"
        description="Purchase log for incoming motorbikes."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/new-motorbike/create')" class="!rounded-none">New arrival</flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, make, model or VIM…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_migrated" placeholder="Allocation">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Allocated</flux:select.option>
                        <flux:select.option value="0">Unallocated</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Allocated</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bikes as $b)
                    <flux:table.row wire:key="nmb-{{ $b->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $b->VRM }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->make }} {{ $b->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->year }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->colour }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->engine }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->status ?: '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $b->is_migrated" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/new-motorbike/'.$b->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $bikes->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
