<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.roles.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Roles</a>
                <span>/</span>
                <span>{{ $roleId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $roleId ? 'Edit role: ' . $name : 'New role' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.roles.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save role</flux:button>
        </div>
    </div>

    @if(session('flux-admin.flash'))
        <div class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
            {{ session('flux-admin.flash') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Role details</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Roles bundle permissions that can be assigned to users.</p>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('name')">
                    <flux:input wire:model="name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Guard" :error="$errors->first('guardName')">
                    <flux:input wire:model="guardName" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Permissions</h2>

            <x-flux-admin::field-group :error="$errors->first('selectedPermissions')" hint="Tick the permissions that should be granted to this role.">
                <div class="mb-2">
                    <flux:input wire:model.live.debounce.250ms="permissionSearch" placeholder="Filter permissions…" />
                </div>
                <div class="border border-zinc-200 dark:border-zinc-800 max-h-[26rem] overflow-y-auto p-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
                    @forelse($permissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 px-1 py-0.5">
                            <input type="checkbox" value="{{ $permission->id }}" wire:model="selectedPermissions" class="accent-zinc-900 dark:accent-zinc-200">
                            <span>{{ $permission->name }}</span>
                        </label>
                    @empty
                        <div class="col-span-full text-sm text-zinc-500 dark:text-zinc-400">No permissions match your filter.</div>
                    @endforelse
                </div>
            </x-flux-admin::field-group>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.roles.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save role</flux:button>
        </div>
    </form>
</div>
