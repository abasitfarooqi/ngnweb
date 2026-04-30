<div>
    <x-flux-admin::form-panel
        :title="$roleId ? 'Edit role: '.$name : 'New role'"
        description="Roles bundle permissions that can be assigned to users."
    >
        @if(session('flux-admin.flash'))
            <div class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('flux-admin.flash') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('name')">
                    <flux:input wire:model="name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Guard" :error="$errors->first('guardName')">
                    <flux:input wire:model="guardName" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Permissions" hint="Tick the permissions that should be granted to this role.">
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

            <x-slot:footer>
                <flux:button size="sm" variant="ghost" :href="route('flux-admin.roles.index')" class="!rounded-none">Back</flux:button>
                <flux:button size="sm" variant="primary" type="submit" class="!rounded-none">Save</flux:button>
            </x-slot:footer>
        </form>
    </x-flux-admin::form-panel>
</div>
