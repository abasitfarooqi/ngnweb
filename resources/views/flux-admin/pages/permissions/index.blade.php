<div>
    <x-flux-admin::data-table
        title="Permissions"
        description="Individual capabilities that can be assigned to roles or users."
    >
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">
                New permission
            </flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search by name…" />
        </x-slot:toolbar>

        @if(session('flux-admin.flash'))
            <div class="border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('flux-admin.flash') }}
            </div>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortField === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column>Guard</flux:table.column>
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($permissions as $permission)
                    <flux:table.row wire:key="perm-{{ $permission->id }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $permission->name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $permission->guard_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $permission->roles_count }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $permission->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="deletePermission({{ $permission->id }})" wire:confirm="Delete this permission?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No permissions found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $permissions->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model="editorOpen" class="md:w-[28rem]">
        <div class="flex flex-col gap-4">
            <flux:heading size="lg">{{ $editingId ? 'Edit permission' : 'New permission' }}</flux:heading>

            <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')" hint="kebab-case, e.g. see-menu-rentals">
                <flux:input wire:model="form.name" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Guard" :error="$errors->first('form.guard_name')">
                <flux:input wire:model="form.guard_name" />
            </x-flux-admin::field-group>

            <div class="flex items-center justify-end gap-2 pt-2">
                <flux:button size="sm" variant="ghost" wire:click="$set('editorOpen', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button size="sm" variant="primary" wire:click="save" class="!rounded-none">Save</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
