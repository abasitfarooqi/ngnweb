<div>
    <x-flux-admin::data-table title="Surveys" description="Manage customer surveys and questionnaires.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.surveys.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New survey</flux:button>
            </a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search title…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_active" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Inactive</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Questions</flux:table.column>
                <flux:table.column>Active</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="sv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white font-medium">{{ $r->title }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->slug ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->questions_count }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:switch :checked="(bool) $r->is_active" wire:click="toggleActive({{ $r->id }})" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.surveys.campaign', $r) }}">
                                    <flux:button size="xs" variant="ghost" class="!rounded-none">Campaign</flux:button>
                                </a>
                                <a href="{{ route('flux-admin.surveys.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this survey?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No surveys.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
