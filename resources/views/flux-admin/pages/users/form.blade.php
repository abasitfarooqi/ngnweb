<div>
    <x-flux-admin::form-panel
        :title="$userId ? 'Edit user' : 'New user'"
        description="Staff account details, role assignment and extra permissions."
    >
        @if(session('flux-admin.flash'))
            <div class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('flux-admin.flash') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                <div class="flex items-end gap-4">
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="is_admin" class="accent-zinc-900 dark:accent-zinc-200"> Admin
                    </label>
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="is_client" class="accent-zinc-900 dark:accent-zinc-200"> Client
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

            <x-flux-admin::field-group label="Roles" :error="$errors->first('selectedRoles')">
                <div class="flex flex-wrap gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center gap-2 border border-zinc-200 dark:border-zinc-800 px-2 py-1 text-sm">
                            <input type="checkbox" value="{{ $role->id }}" wire:model="selectedRoles" class="accent-zinc-900 dark:accent-zinc-200">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Extra permissions" hint="Granted in addition to the user's role permissions.">
                <div class="mb-2">
                    <flux:input wire:model.live.debounce.250ms="permissionSearch" placeholder="Filter permissions…" />
                </div>
                <div class="border border-zinc-200 dark:border-zinc-800 max-h-[22rem] overflow-y-auto p-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
                    @forelse($permissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                            <input type="checkbox" value="{{ $permission->id }}" wire:model="selectedPermissions" class="accent-zinc-900 dark:accent-zinc-200">
                            <span>{{ $permission->name }}</span>
                        </label>
                    @empty
                        <div class="col-span-full text-sm text-zinc-500 dark:text-zinc-400">No permissions match your filter.</div>
                    @endforelse
                </div>
            </x-flux-admin::field-group>

            <x-slot:footer>
                <flux:button size="sm" variant="ghost" :href="route('flux-admin.users.index')" class="!rounded-none">Back</flux:button>
                <flux:button size="sm" variant="primary" type="submit" class="!rounded-none">Save</flux:button>
            </x-slot:footer>
        </form>
    </x-flux-admin::form-panel>
</div>
