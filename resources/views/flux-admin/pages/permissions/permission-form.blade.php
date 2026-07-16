<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.permissions.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Permissions</a>
                <span>/</span>
                <span>{{ $permissionId ? 'Edit' : 'New permission' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $permissionId ? 'Edit permission' : 'New permission' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.permissions.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save permission</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Permission details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 {{ $multipleGuards ? 'md:grid-cols-2' : '' }} gap-4">
                <x-flux-admin::field-group label="Permission name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" placeholder="e.g. see-menu-customers" />
                </x-flux-admin::field-group>
                @if($multipleGuards)
                    <x-flux-admin::field-group label="Guard name" required :error="$errors->first('form.guard_name')">
                        <flux:select wire:model="form.guard_name" placeholder="Select guard">
                            @foreach($guardOptions as $guard)
                                <flux:select.option value="{{ $guard }}">{{ $guard }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </x-flux-admin::field-group>
                @endif
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.permissions.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save permission</flux:button>
        </div>
    </form>
</div>
