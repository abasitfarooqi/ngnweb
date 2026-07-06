<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-new.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">New motorbikes</a>
                <span>/</span>
                <span>{{ $newMotorbike && $newMotorbike->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $newMotorbike && $newMotorbike->exists ? 'Edit motorbike' : 'Add new motorbike' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-new.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Motorbike details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.VRM')">
                    <flux:input wire:model="form.VRM" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" placeholder="e.g. 2024" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Engine" :error="$errors->first('form.engine')">
                    <flux:input wire:model="form.engine" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VIM" :error="$errors->first('form.VIM')">
                    <flux:input wire:model="form.VIM" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" :error="$errors->first('form.status')">
                    <flux:input wire:model="form.status" placeholder="e.g. available" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Purchase date" :error="$errors->first('form.purchase_date')">
                    <flux:input type="date" wire:model="form.purchase_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="— Any —">
                        <flux:select.option value="">— Any —</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_migrated" class="accent-zinc-900 dark:accent-zinc-200"> Allocated (migrated)
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-new.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
