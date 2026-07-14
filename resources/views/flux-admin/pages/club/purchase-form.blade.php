<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.club-purchases.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Club Purchases</a>
                <span>/</span>
                <span>{{ $purchase ? 'Edit' : 'New purchase' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $purchase ? 'Edit purchase #'.$purchase->id : 'New club purchase' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.club-purchases.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save purchase</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Purchase details</h2>
            <div class="mb-4">
                <x-flux-admin::field-group label="Club member" required :error="$errors->first('form.club_member_id')">
                    <div class="{{ count($memberSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input wire:model.live.debounce.300ms="memberSearch" placeholder="Search name, phone, email or VRM…" autocomplete="off" />
                        @if(count($memberSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($memberSuggestions as $s)
                                    <li role="option" wire:mousedown.prevent="selectClubMember({{ $s['id'] }})">{{ $s['label'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Date" required :error="$errors->first('form.date')">
                    <flux:input type="date" wire:model="form.date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS invoice" :error="$errors->first('form.pos_invoice')">
                    <flux:input wire:model.live.debounce.300ms="form.pos_invoice" />
                    @if($posInvoiceWarning)
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">{{ $posInvoiceWarning }}</p>
                    @endif
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total (£)" required :error="$errors->first('form.total')">
                    <flux:input type="number" step="0.01" wire:model.live.debounce.300ms="form.total" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Percent (%)" required :error="$errors->first('form.percent')">
                    <flux:input type="number" step="0.01" wire:model.live.debounce.300ms="form.percent" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Discount (£)" required :error="$errors->first('form.discount')">
                    <flux:input type="number" step="0.01" wire:model="form.discount" :readonly="$autoDiscount" />
                    <label class="mt-2 flex items-center gap-2 text-xs font-medium text-zinc-600 dark:text-zinc-400">
                        <input type="checkbox" wire:model.live="autoDiscount" class="accent-zinc-900 dark:accent-zinc-200">
                        Auto calculate discount
                    </label>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="Select branch">
                        <flux:select.option value="">None</flux:select.option>
                        <flux:select.option value="CATFORD">CATFORD</flux:select.option>
                        <flux:select.option value="SUTTON">SUTTON</flux:select.option>
                        <flux:select.option value="TOOTING">TOOTING</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_redeemed" class="accent-zinc-900 dark:accent-zinc-200"> Redeemed
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.club-purchases.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save purchase</flux:button>
        </div>
    </form>
</div>
