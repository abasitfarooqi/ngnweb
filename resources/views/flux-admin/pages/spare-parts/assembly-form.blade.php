<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.sp-assemblies.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Assemblies</a>
                <span>/</span>
                <span>{{ $spAssembly ? 'Edit' : 'New assembly' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $spAssembly ? 'Edit assembly: '.$spAssembly->name : 'New assembly' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.sp-assemblies.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save assembly</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Assembly details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Fitment" required :error="$errors->first('form.fitment_id')">
                    <flux:select wire:model="form.fitment_id" placeholder="Select fitment">
                        <flux:select.option value="">Select…</flux:select.option>
                        @foreach($fitments as $f)
                            <flux:select.option value="{{ $f->id }}">#{{ $f->id }} — {{ $f->model?->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Slug" :error="$errors->first('form.slug')" hint="Auto-generated if empty.">
                    <flux:input wire:model="form.slug" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="External ID" :error="$errors->first('form.external_id')">
                    <flux:input wire:model="form.external_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sort order" :error="$errors->first('form.sort_order')">
                    <flux:input type="number" wire:model="form.sort_order" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Image URL" :error="$errors->first('form.image_url')">
                    <flux:input wire:model="form.image_url" placeholder="https://…" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Diagram URL" :error="$errors->first('form.diagram_url')">
                    <flux:input wire:model="form.diagram_url" placeholder="https://…" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.sp-assemblies.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save assembly</flux:button>
        </div>
    </form>
</div>
