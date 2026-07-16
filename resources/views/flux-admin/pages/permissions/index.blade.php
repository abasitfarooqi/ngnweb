<div>
    <x-flux-admin::data-table
        title="Permissions"
        description="Individual capabilities that can be assigned to roles or users."
    >
        <x-slot:actions>
            @if($allowCreate)
                <flux:button size="sm" variant="primary" icon="plus" :href="route('flux-admin.permissions.create')" class="!rounded-none">
                    New permission
                </flux:button>
            @endif
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
                @if($multipleGuards)
                    <flux:table.column sortable :sorted="$sortField === 'guard_name'" :direction="$sortField === 'guard_name' ? $sortDirection : null" wire:click="sortBy('guard_name')">Guard</flux:table.column>
                @endif
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($permissions as $permission)
                    <flux:table.row wire:key="perm-{{ $permission->id }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $permission->name }}</flux:table.cell>
                        @if($multipleGuards)
                            <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $permission->guard_name }}</flux:table.cell>
                        @endif
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $permission->roles_count }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                @if($allowUpdate)
                                    <flux:button size="xs" variant="ghost" :href="route('flux-admin.permissions.edit', $permission->id)" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                @endif
                                @if($allowDelete)
                                    <flux:button size="xs" variant="danger" wire:click="deletePermission({{ $permission->id }})" wire:confirm="Delete this permission?" icon="trash" class="!rounded-none">Delete</flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ $multipleGuards ? 4 : 3 }}" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No permissions found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $permissions->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
