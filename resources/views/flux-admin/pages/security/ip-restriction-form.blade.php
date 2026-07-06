<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.ip-restrictions.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">IP Restrictions</a>
                <span>/</span>
                <span>{{ $ipRestriction ? 'Edit' : 'New restriction' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $ipRestriction ? 'Edit restriction: '.$ipRestriction->ip_address : 'New IP restriction' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.ip-restrictions.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save restriction</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Restriction details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="IP address" required :error="$errors->first('form.ip_address')">
                    <flux:input wire:model="form.ip_address" placeholder="e.g. 192.168.1.1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Label" :error="$errors->first('form.label')">
                    <flux:input wire:model="form.label" placeholder="Optional description" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="blocked">Blocked</flux:select.option>
                        <flux:select.option value="allowed">Allowed</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Restriction type" required :error="$errors->first('form.restriction_type')">
                    <flux:select wire:model="form.restriction_type">
                        <flux:select.option value="full_site">Full site</flux:select.option>
                        <flux:select.option value="admin_only">Admin only</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="User ID (optional)" :error="$errors->first('form.user_id')">
                    <flux:input type="number" wire:model="form.user_id" placeholder="Leave blank for global" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.ip-restrictions.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save restriction</flux:button>
        </div>
    </form>
</div>
