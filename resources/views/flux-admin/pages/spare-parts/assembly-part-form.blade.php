<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.sp-assembly-parts.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Assembly parts</a>
                <span>/</span>
                <span>{{ $spAssemblyPart && $spAssemblyPart->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $spAssemblyPart && $spAssemblyPart->exists ? 'Edit assembly part' : 'New assembly part' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.sp-assembly-parts.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <x-flux-admin::field-group label="Assembly" required :error="$errors->first('form.assembly_id')">
                <flux:select wire:model="form.assembly_id" placeholder="— Select —">
                    @foreach($assemblies as $a)
                        <flux:select.option value="{{ $a->id }}">{{ $a->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <div class="mt-4">
                <x-flux-admin::field-group label="Part" required :error="$errors->first('form.part_id')">
                    <flux:select wire:model="form.part_id" placeholder="— Select —">
                        @foreach($parts as $p)
                            <flux:select.option value="{{ $p->id }}">{{ $p->part_number }} · {{ $p->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-flux-admin::field-group label="Qty used" required :error="$errors->first('form.qty_used')">
                    <flux:input type="number" min="1" wire:model="form.qty_used" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sort order" :error="$errors->first('form.sort_order')">
                    <flux:input type="number" min="0" wire:model="form.sort_order" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price override" :error="$errors->first('form.price_override')">
                    <flux:input type="number" step="0.01" wire:model="form.price_override" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Stock override" :error="$errors->first('form.stock_override')">
                    <flux:input type="number" step="0.01" wire:model="form.stock_override" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note override" :error="$errors->first('form.note_override')">
                    <flux:textarea wire:model="form.note_override" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.sp-assembly-parts.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
