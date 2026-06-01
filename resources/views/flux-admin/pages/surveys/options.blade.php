<div>
    <x-flux-admin::data-table title="Survey options" description="Answer options for radio / select / checkbox questions.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.survey-options.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New option</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search option text…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Survey</flux:table.column>
                <flux:table.column>Question</flux:table.column>
                <flux:table.column>Option text</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="so-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->question?->survey?->title ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 truncate max-w-[180px]">{{ $r->question?->question_text ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->option_text }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.survey-options.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete?" icon="trash" class="!rounded-none text-red-600">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-zinc-500">No options.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
