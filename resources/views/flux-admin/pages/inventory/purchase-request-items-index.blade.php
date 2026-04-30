<div>
    <x-flux-admin::data-table title="Purchase request items" description="Individual parts across all purchase requests.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/purchase-request-item/create')" class="!rounded-none">New item</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search part #, reg or chassis…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:input wire:model.live.debounce.400ms="filters.pr_id" placeholder="PR #" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>PR #</flux:table.column>
                <flux:table.column>Part number</flux:table.column>
                <flux:table.column>Position</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Chassis</flux:table.column>
                <flux:table.column>Qty</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="pri-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->pr_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->part_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->part_position }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->reg_no }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->chassis_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->quantity }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/purchase-request-item/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
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
