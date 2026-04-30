<div>
    <x-flux-admin::data-table
        title="Users"
        description="Staff accounts with access to the admin area."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="route('flux-admin.users.create')" class="!rounded-none">
                New user
            </flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search by name, email or username…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.role" placeholder="Role">
                        <flux:select.option value="">All roles</flux:select.option>
                        @foreach($roles as $role)
                            <flux:select.option value="{{ $role->id }}">{{ $role->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.admin" placeholder="Admin">
                        <flux:select.option value="">All users</flux:select.option>
                        <flux:select.option value="1">Admin only</flux:select.option>
                        <flux:select.option value="0">Non-admin</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        @if(session('flux-admin.flash'))
            <div class="border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('flux-admin.flash') }}
            </div>
        @endif
        @if(session('flux-admin.error'))
            <div class="border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
                {{ session('flux-admin.error') }}
            </div>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'first_name'" :direction="$sortField === 'first_name' ? $sortDirection : null" wire:click="sortBy('first_name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortField === 'email' ? $sortDirection : null" wire:click="sortBy('email')">Email</flux:table.column>
                <flux:table.column>Username</flux:table.column>
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>Admin</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($users as $user)
                    <flux:table.row wire:key="user-{{ $user->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.users.show', $user) }}" class="font-medium text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                {{ trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->email }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $user->email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $user->username }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $user->roles->pluck('name')->implode(', ') ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="(bool) $user->is_admin" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="xs" variant="ghost" :href="route('flux-admin.users.edit', $user)" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete this user? This cannot be undone." icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No users found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $users->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
