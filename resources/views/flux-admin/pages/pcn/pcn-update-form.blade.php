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
                    <div class="{{ count($caseSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input wire:model.live.debounce.300ms="caseSearch" placeholder="Search by PCN number…" autocomplete="off" />
                        @if(count($caseSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($caseSuggestions as $s)
                                    <li role="option" wire:mousedown.prevent="selectCase({{ $s['id'] }})">{{ $s['label'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Update date" required :error="$errors->first('form.update_date')">
                    <flux:input type="datetime-local" wire:model="form.update_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Additional fee (£)" :error="$errors->first('form.additional_fee')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.additional_fee" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Note" required :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" placeholder="Include Office365 document link if available" />
                </x-flux-admin::field-group>
            </div>

            <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                Cancelled and paid-by-keeper flags reverse PCN dues across the system, as in Backpack.
            </p>

            <div class="mt-4 flex flex-wrap gap-5">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_appealed" class="accent-zinc-900 dark:accent-zinc-200"> Appealed
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_tol_requested" class="accent-zinc-900 dark:accent-zinc-200"> TOL Request
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_appeal_rejected" class="accent-zinc-900 dark:accent-zinc-200"> Appeal Rejected
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
