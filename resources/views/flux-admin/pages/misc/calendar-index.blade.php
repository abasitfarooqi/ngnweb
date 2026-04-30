<div>
    <x-flux-admin::data-table title="Calendar events" description="Internal events calendar (admin schedules, reminders, milestones).">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New event</flux:button>
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
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[560px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit event' : 'New event' }}</flux:heading>
            <x-flux-admin::field-group label="Title" :error="$errors->first('formData.title')" required>
                <flux:input wire:model="formData.title" />
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Starts" :error="$errors->first('formData.start')" required>
                    <flux:input type="datetime-local" wire:model="formData.start" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Ends" :error="$errors->first('formData.end')">
                    <flux:input type="datetime-local" wire:model="formData.end" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Background colour">
                    <flux:input wire:model="formData.background_color" placeholder="#2563eb" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Text colour">
                    <flux:input wire:model="formData.text_color" placeholder="#ffffff" />
                </x-flux-admin::field-group>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
