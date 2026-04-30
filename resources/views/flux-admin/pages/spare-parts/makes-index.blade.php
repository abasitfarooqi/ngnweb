<div>
    <x-flux-admin::data-table title="Spare parts · Makes" description="Motorbike manufacturers in the spare parts catalogue.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New make</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_active" placeholder="Active">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Inactive</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Source</flux:table.column>
                <flux:table.column>Active</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="spm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->slug }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->source }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_active" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this make?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[480px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit make' : 'New make' }}</flux:heading>
            <x-flux-admin::field-group label="Name" :error="$errors->first('formData.name')" required>
                <flux:input wire:model="formData.name" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Slug" :error="$errors->first('formData.slug')" hint="Leave empty to auto-generate.">
                <flux:input wire:model="formData.slug" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Source" :error="$errors->first('formData.source')" hint="e.g. manual, cmsnl, etc.">
                <flux:input wire:model="formData.source" />
            </x-flux-admin::field-group>
            <flux:checkbox wire:model="formData.is_active" label="Active" />
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
