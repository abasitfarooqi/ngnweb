<div>
    <x-flux-admin::data-table
        title="Roles"
        description="Groups of permissions that can be assigned to users."
    >
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="route('flux-admin.roles.create')" class="!rounded-none">
                New role
            </flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search by name…" />
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortField === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column>Guard</flux:table.column>
                <flux:table.column>Users</flux:table.column>
                <flux:table.column>Permissions</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($roles as $role)
                    <flux:table.row wire:key="role-{{ $role->id }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            <a href="{{ route('flux-admin.roles.edit', $role) }}" class="hover:text-zinc-600 dark:hover:text-zinc-300">{{ $role->name }}</a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $role->guard_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $role->users_count }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $role->permissions_count }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="route('flux-admin.roles.edit', $role)" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No roles found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $roles->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
