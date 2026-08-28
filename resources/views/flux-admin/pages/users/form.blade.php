<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.users.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Users</a>
                <span>/</span>
                <span>{{ $userId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $userId ? 'Edit user' : 'New user' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.users.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save user</flux:button>
        </div>
    </div>

    @if(session('flux-admin.flash'))
        <div class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
            {{ session('flux-admin.flash') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Account details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="First name" required :error="$errors->first('first_name')">
                    <flux:input wire:model="first_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Last name" :error="$errors->first('last_name')">
                    <flux:input wire:model="last_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" required :error="$errors->first('email')">
                    <flux:input type="email" wire:model="email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Username" required :error="$errors->first('username')">
                    <flux:input wire:model="username" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Employee ID" :error="$errors->first('employee_id')">
                    <flux:input wire:model="employee_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Primary role" required :error="$errors->first('role_id')">
                    <flux:select wire:model.live="role_id" placeholder="Select role">
                        @foreach($roles as $role)
                            <flux:select.option value="{{ $role->id }}">{{ $role->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <div class="flex items-end gap-6 pb-1 sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="is_admin" class="accent-zinc-900 dark:accent-zinc-200"> Admin
                    </label>
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="is_client" class="accent-zinc-900 dark:accent-zinc-200"> Client
                    </label>
                </div>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Password</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group
                    label="Password"
                    :required="! $userId"
                    :error="$errors->first('password')"
                    :hint="$userId ? 'Leave blank to keep the current password.' : null"
                >
                    <flux:input type="password" wire:model="password" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Confirm password" :required="! $userId">
                    <flux:input type="password" wire:model="password_confirmation" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Roles</h2>
            <p class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Untick Super Admin to remove it. Primary role follows the ticks — it will not put Super Admin back on save.</p>

            <x-flux-admin::field-group :error="$errors->first('selectedRoles')">
                <div class="flex flex-wrap gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center gap-2 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-sm">
                            <input type="checkbox" value="{{ $role->id }}" wire:model.live="selectedRoles" class="accent-zinc-900 dark:accent-zinc-200">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-flux-admin::field-group>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Permissions</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Permissions inherited from roles are shown for reference. Extra permissions are granted directly to this user. Tick manage-communications for the Communications control panel, and view-notifications for the Notifications page. They are separate.</p>

            <x-flux-admin::field-group hint="Granted in addition to the user's role permissions.">
                <div class="mb-2">
                    <flux:input wire:model.live.debounce.250ms="permissionSearch" placeholder="Filter permissions…" />
                </div>
                <div class="border border-zinc-200 dark:border-zinc-800 max-h-[22rem] overflow-y-auto p-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
                    @forelse($permissions as $permission)
                        @php($inherited = in_array($permission->id, $inheritedPermissionIds, true))
                        <label class="flex items-center gap-2 text-sm {{ $inherited ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-700 dark:text-zinc-300' }}">
                            @if($inherited)
                                <input type="checkbox" checked disabled class="accent-zinc-400">
                                <span>{{ $permission->name }} <span class="text-xs">(via role)</span></span>
                            @else
                                <input type="checkbox" value="{{ $permission->id }}" wire:model="selectedPermissions" class="accent-zinc-900 dark:accent-zinc-200">
                                <span>{{ $permission->name }}</span>
                            @endif
                        </label>
                    @empty
                        <div class="col-span-full text-sm text-zinc-500 dark:text-zinc-400">No permissions match your filter.</div>
                    @endforelse
                </div>
            </x-flux-admin::field-group>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.users.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save user</flux:button>
        </div>
    </form>
</div>
