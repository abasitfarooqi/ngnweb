<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.sp-fitments.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Fitments</a>
                <span>/</span>
                <span>{{ $spFitment ? 'Edit' : 'New fitment' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $spFitment ? 'Edit fitment #'.$spFitment->id : 'New fitment' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.sp-fitments.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save fitment</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Fitment details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Model" required :error="$errors->first('form.model_id')">
                    <flux:select wire:model="form.model_id" placeholder="Select model">
                        <flux:select.option value="">Select…</flux:select.option>
                        @foreach($models as $m)
                            <flux:select.option value="{{ $m->id }}">{{ $m->make?->name }} — {{ $m->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" min="1900" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Country name" :error="$errors->first('form.country_name')">
                    <flux:input wire:model="form.country_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Country slug" :error="$errors->first('form.country_slug')" hint="Auto-generated if empty.">
                    <flux:input wire:model="form.country_slug" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour name" :error="$errors->first('form.colour_name')">
                    <flux:input wire:model="form.colour_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour slug" :error="$errors->first('form.colour_slug')" hint="Auto-generated if empty.">
                    <flux:input wire:model="form.colour_slug" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.sp-fitments.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save fitment</flux:button>
        </div>
    </form>
</div>
