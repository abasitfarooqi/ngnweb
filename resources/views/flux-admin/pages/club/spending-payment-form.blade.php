<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.club-spending-payments.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Club Spending Payments</a>
                <span>/</span>
                <span>{{ $spendingPayment ? 'Edit' : 'New payment' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $spendingPayment ? 'Edit payment #'.$spendingPayment->id : 'New spending payment' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.club-spending-payments.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save payment</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Payment details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Club member ID" required :error="$errors->first('form.club_member_id')">
                    <flux:input type="number" wire:model.live.debounce.300ms="form.club_member_id" min="1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total spending">
                    <flux:input :value="$totalSpending !== null ? '£'.number_format($totalSpending, 2) : ''" readonly />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total unpaid">
                    <flux:input :value="$totalUnpaid !== null ? '£'.number_format($totalUnpaid, 2) : ''" readonly />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date" required :error="$errors->first('form.date')">
                    <flux:input type="date" wire:model="form.date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Spending ID" :error="$errors->first('form.spending_id')">
                    <flux:input type="number" wire:model="form.spending_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS invoice" :error="$errors->first('form.pos_invoice')">
                    <flux:input wire:model="form.pos_invoice" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Received total (£)" required :error="$errors->first('form.received_total')">
                    <flux:input type="number" step="0.01" wire:model="form.received_total" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" required :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="Select branch">
                        <flux:select.option value="">Select branch</flux:select.option>
                        <flux:select.option value="CATFORD">CATFORD</flux:select.option>
                        <flux:select.option value="SUTTON">SUTTON</flux:select.option>
                        <flux:select.option value="TOOTING">TOOTING</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <label class="mt-4 flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                <flux:checkbox wire:model="form.include_today" />
                Include today's spendings when applying this payment
            </label>
            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.club-spending-payments.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save payment</flux:button>
        </div>
    </form>
</div>
