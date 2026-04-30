<div>
    <x-flux-admin::data-table title="Blog categories" description="Organise blog posts by topic.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New category</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search name…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Group</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="bc-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->slug }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->blog_category }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="openEdit({{ $r->id }})" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $r->id }})" wire:confirm="Delete this category?" class="!rounded-none text-red-600">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model="showForm" class="md:w-[32rem]">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $recordId ? 'Edit category' : 'New category' }}</flux:heading>
            <x-flux-admin::field-group label="Name" :error="$errors->first('formData.name')" required>
                <flux:input wire:model="formData.name" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Slug" hint="Auto-generated from name if left blank." :error="$errors->first('formData.slug')">
                <flux:input wire:model="formData.slug" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Group" hint="Optional grouping key." :error="$errors->first('formData.blog_category')">
                <flux:input wire:model="formData.blog_category" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button variant="primary" wire:click="saveForm" class="!rounded-none">Save</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
