<div>
    <x-flux-admin::data-table title="Employee schedules" description="Off-day assignments per staff member.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.employee-schedules.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">Assign off day</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search employee name…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Off day</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="es-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->user ? trim(($r->user->first_name ?? '').' '.($r->user->last_name ?? '')) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->off_day }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.employee-schedules.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Remove this assignment?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="3" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No schedules.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
