<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.pcn-updates.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>PCN case updates</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit PCN update' : 'New PCN update' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.pcn-updates.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Update details</h2>

            <div class="mb-4">
                <x-flux-admin::field-group label="PCN case" required :error="$errors->first('form.case_id')">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="caseSearch" placeholder="Search by PCN number…" autocomplete="off" />
                        @if(count($caseSuggestions))
                            <ul class="absolute z-50 mt-0.5 w-full border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-44 overflow-y-auto">
                                @foreach($caseSuggestions as $s)
                                    <li wire:click="selectCase({{ $s['id'] }}, '{{ addslashes($s['label']) }}')"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ $s['label'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Update date" required :error="$errors->first('form.update_date')">
                    <flux:input type="date" wire:model="form.update_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Additional fee (£)" :error="$errors->first('form.additional_fee')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.additional_fee" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-wrap gap-5">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_appealed" class="accent-zinc-900 dark:accent-zinc-200"> Appealed
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_paid_by_owner" class="accent-zinc-900 dark:accent-zinc-200"> Paid by NGN
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_paid_by_keeper" class="accent-zinc-900 dark:accent-zinc-200"> Paid by keeper
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_transferred" class="accent-zinc-900 dark:accent-zinc-200"> Transferred
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_cancled" class="accent-zinc-900 dark:accent-zinc-200"> Cancelled
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.pcn-updates.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
