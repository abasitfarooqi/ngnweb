<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.branches.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Branches</a>
                <span>/</span>
                <span>{{ $branch ? 'Edit' : 'New branch' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $branch ? 'Edit branch: '.$branch->name : 'New branch' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.branches.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save branch</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Branch details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="City" :error="$errors->first('form.city')">
                    <flux:input wire:model="form.city" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Postal code" :error="$errors->first('form.postal_code')">
                    <flux:input wire:model="form.postal_code" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Latitude" :error="$errors->first('form.latitude')">
                    <flux:input type="number" step="0.000001" wire:model="form.latitude" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Longitude" :error="$errors->first('form.longitude')">
                    <flux:input type="number" step="0.000001" wire:model="form.longitude" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Address" :error="$errors->first('form.address')">
                    <flux:textarea wire:model="form.address" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.branches.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save branch</flux:button>
        </div>
    </form>
</div>
