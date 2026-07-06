<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-claims.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Motorbike claims</a>
                <span>/</span>
                <span>{{ $claimMotorbike && $claimMotorbike->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $claimMotorbike && $claimMotorbike->exists ? 'Edit claim' : 'New claim' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-claims.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Claim details</h2>

            {{-- Motorbike search --}}
            <div class="mb-4">
                <x-flux-admin::field-group label="Motorbike (reg)" :error="$errors->first('form.motorbike_id')">
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

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Full name" required :error="$errors->first('form.fullname')">
                    <flux:input wire:model="form.fullname" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Case date" required :error="$errors->first('form.case_date')">
                    <flux:input type="date" wire:model="form.case_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id">
                        <flux:select.option value="">— None —</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="form.is_received" class="accent-zinc-900 dark:accent-zinc-200"> Received
                    </label>
                    <x-flux-admin::field-group label="Received date" :error="$errors->first('form.received_date')">
                        <flux:input type="date" wire:model="form.received_date" />
                    </x-flux-admin::field-group>
                </div>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <input type="checkbox" wire:model="form.is_returned" class="accent-zinc-900 dark:accent-zinc-200"> Returned
                    </label>
                    <x-flux-admin::field-group label="Returned date" :error="$errors->first('form.returned_date')">
                        <flux:input type="date" wire:model="form.returned_date" />
                    </x-flux-admin::field-group>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-claims.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
