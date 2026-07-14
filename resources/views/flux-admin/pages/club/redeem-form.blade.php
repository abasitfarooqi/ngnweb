<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.club-redemptions.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Club Redemptions</a>
                <span>/</span>
                <span>{{ $redeem ? 'Edit' : 'New redemption' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $redeem ? 'Edit redemption #'.$redeem->id : 'New club redemption' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.club-redemptions.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save redemption</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Redemption details</h2>
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
                <x-flux-admin::field-group label="Available balance">
                    <flux:input :value="$remainingBalance !== null ? '£'.number_format($remainingBalance, 2) : ''" readonly />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date" required :error="$errors->first('form.date')">
                    <flux:input type="date" wire:model="form.date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Redeem total (£)" required :error="$errors->first('form.redeem_total')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.redeem_total" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS invoice" :error="$errors->first('form.pos_invoice')">
                    <flux:input wire:model="form.pos_invoice" />
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
            @if($hasTodayPurchases)
                <div class="mt-4 border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100">
                    <p class="mb-2">This member has purchases from today.</p>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="form.include_today" class="accent-zinc-900 dark:accent-zinc-200">
                        Include today's purchases in this redemption
                    </label>
                </div>
            @endif
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>
            <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Saving marks unredeemed purchase discounts as redeemed and overwrites redeem total with the sum applied.</p>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.club-redemptions.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save redemption</flux:button>
        </div>
    </form>
</div>
