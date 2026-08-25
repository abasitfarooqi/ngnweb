<div>
        <x-flux-admin::data-table title="Repair updates" description="Job lines on each repair. Open a repair to add or edit them.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.motorbike-repair-updates.create') }}" wire:navigate>
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New update</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search job, note, customer, registration or repair ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.motorbike_repair_id" placeholder="Repair ID" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Repair</flux:table.column>
                <flux:table.column>Job description</flux:table.column>
                <flux:table.column>Services</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="mru-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            <a href="{{ route('flux-admin.motorbike-repairs.edit', $r->motorbike_repair_id) }}" class="hover:underline" wire:navigate>
                                #{{ $r->motorbike_repair_id }}
                                @if($r->motorbikeRepair?->motorbike?->reg_no)
                                    · {{ $r->motorbikeRepair->motorbike->reg_no }}
                                @endif
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->job_description }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-xs">
                            {{ $r->services->pluck('name')->join(', ') ?: '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $r->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $r->note ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.motorbike-repairs.edit', $r->motorbike_repair_id) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit on repair</flux:button>
                                </a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $r->id }})" wire:confirm="Delete this update?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
