<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->email }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">User profile, roles and extra permissions.</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:button size="sm" variant="ghost" :href="route('flux-admin.users.index')" class="!rounded-none">Back</flux:button>
            <flux:button size="sm" variant="primary" :href="route('flux-admin.users.edit', $user)" icon="pencil-square" class="!rounded-none">Edit</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Profile</h2>
            <dl class="text-sm divide-y divide-zinc-100 dark:divide-zinc-800">
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Email</dt><dd class="text-zinc-900 dark:text-white">{{ $user->email }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Username</dt><dd class="text-zinc-900 dark:text-white">{{ $user->username }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Admin</dt><dd><x-flux-admin::status-badge :status="(bool) $user->is_admin" /></dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Client</dt><dd><x-flux-admin::status-badge :status="(bool) $user->is_client" /></dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Employee ID</dt><dd class="text-zinc-900 dark:text-white">{{ $user->employee_id ?? '—' }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Created</dt><dd class="text-zinc-900 dark:text-white">{{ $user->created_at?->format('d M Y H:i') }}</dd></div>
            </dl>
        </div>

        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Roles</h2>
            @if($user->roles->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No roles assigned.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                        <flux:badge color="blue" size="sm">{{ $role->name }}</flux:badge>
                    @endforeach
                </div>
            @endif

            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mt-5 mb-3">Extra permissions</h2>
            @if($user->permissions->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No extra permissions beyond the user's roles.</p>
            @else
                <div class="flex flex-wrap gap-1">
                    @foreach($user->permissions as $permission)
                        <flux:badge color="zinc" size="sm">{{ $permission->name }}</flux:badge>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
