<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.motorbike-repair-updates.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Repair updates</a>
                <span>/</span>
                <span>{{ $motorbikeRepairUpdate && $motorbikeRepairUpdate->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motorbikeRepairUpdate && $motorbikeRepairUpdate->exists ? 'Edit repair update' : 'New repair update' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-repair-updates.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flux-admin-form-grid grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-flux-admin::field-group label="Motorbike repair ID" required :error="$errors->first('form.motorbike_repair_id')">
                    <flux:input type="number" wire:model="form.motorbike_repair_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price (£)" required :error="$errors->first('form.price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.price" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Job description" required :error="$errors->first('form.job_description')">
                    <flux:textarea wire:model="form.job_description" rows="3" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Services" :error="$errors->first('form.services')">
                    <div class="max-h-52 overflow-y-auto border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach($services as $service)
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" value="{{ $service->id }}" wire:model="form.services" class="accent-zinc-900 dark:accent-zinc-200">
                                    <span class="min-w-0 flex-1 truncate">{{ $service->name }}</span>
                                    @if($service->price)
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">£{{ number_format((float) $service->price, 2) }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-repair-updates.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
