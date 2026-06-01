<div>
    <x-flux-admin::data-table title="Calendar events" description="Internal events calendar (admin schedules, reminders, milestones).">
        <x-slot:actions>
            <a href="{{ route('flux-admin.calendar.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New event</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search title…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'start'" :direction="$sortField === 'start' ? $sortDirection : null" wire:click="sortBy('start')">Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cal-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->start ? \Carbon\Carbon::parse($r->start)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->end ? \Carbon\Carbon::parse($r->end)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->title }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs" style="background: {{ $r->background_color ?: '#e5e7eb' }}; color: {{ $r->text_color ?: '#000' }}">{{ $r->background_color ?: '—' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.calendar.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this event?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No events.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
