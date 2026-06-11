<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-repairs.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Motorbike repairs</a>
                <span>/</span>
                <span>{{ $motorbikeRepair && $motorbikeRepair->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motorbikeRepair && $motorbikeRepair->exists ? 'Edit repair' : 'New repair' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-repairs.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Repair details</h2>

            {{-- Motorbike search --}}
            <div class="mb-4">
                <x-flux-admin::field-group label="Motorbike (reg)" required :error="$errors->first('form.motorbike_id')">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="motorbikeSearch" placeholder="Search by registration…" autocomplete="off" />
                        @if(count($motorbikeSuggestions))
                            <ul class="absolute z-50 mt-0.5 w-full border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-44 overflow-y-auto">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li wire:click="selectMotorbike({{ $ms['id'] }}, '{{ addslashes($ms['reg']) }}')"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ $ms['reg'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('form.fullname')">
                    <flux:input wire:model="form.fullname" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" type="tel" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input wire:model="form.email" type="email" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Arrival date" :error="$errors->first('form.arrival_date')">
                    <flux:input wire:model="form.arrival_date" type="date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id">
                        <flux:select.option value="">— None —</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Repaired date" :error="$errors->first('form.repaired_date')">
                    <flux:input wire:model="form.repaired_date" type="date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Returned date" :error="$errors->first('form.returned_date')">
                    <flux:input wire:model="form.returned_date" type="date" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-5 border-t border-zinc-200 pt-5 dark:border-zinc-800">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Observation notes</h3>
                    <flux:button type="button" size="sm" variant="subtle" icon="plus" wire:click="addObservation" class="!rounded-none">Add observation</flux:button>
                </div>
                <div class="space-y-3">
                    @forelse($observations as $index => $observation)
                        <div class="flex gap-2" wire:key="repair-observation-{{ $index }}">
                            <div class="min-w-0 flex-1">
                                <x-flux-admin::field-group label="Observation" :error="$errors->first('observations.'.$index.'.observation_description')">
                                    <flux:textarea wire:model="observations.{{ $index }}.observation_description" rows="2" />
                                </x-flux-admin::field-group>
                            </div>
                            <div class="pt-7">
                                <flux:button type="button" size="sm" variant="ghost" icon="trash" wire:click="removeObservation({{ $index }})" class="!rounded-none text-red-600 dark:text-red-400">Remove</flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            No observation notes.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_repaired" class="accent-zinc-900 dark:accent-zinc-200"> Repaired
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_returned" class="accent-zinc-900 dark:accent-zinc-200"> Returned
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-repairs.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
