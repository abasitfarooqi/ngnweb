<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.booking-invoices.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Booking Invoices</a>
                <span>/</span>
                <span>{{ $invoiceId ? 'Edit invoice' : 'New invoice' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $invoiceId ? 'Edit booking invoice' : 'New booking invoice' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.booking-invoices.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save invoice</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Invoice details</h2>

            <x-flux-admin::field-group label="Booking ID" required :error="$errors->first('form.booking_id')">
                <flux:input type="number" wire:model="form.booking_id" />
            </x-flux-admin::field-group>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <x-flux-admin::field-group label="Invoice date" :error="$errors->first('form.invoice_date')">
                    <flux:input type="date" wire:model="form.invoice_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="State" :error="$errors->first('form.state')">
                    <flux:input wire:model="form.state" placeholder="e.g. pending" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Amount (£)" :error="$errors->first('form.amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Deposit (£)" :error="$errors->first('form.deposit')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.deposit" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.live="form.is_paid" class="accent-zinc-900 dark:accent-zinc-200"> Paid
                </label>
            </div>

            @if(!empty($form['is_paid']))
                <div class="mt-4">
                    <x-flux-admin::field-group label="Paid date" :error="$errors->first('form.paid_date')">
                        <flux:input type="date" wire:model="form.paid_date" />
                    </x-flux-admin::field-group>
                </div>
            @endif

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.booking-invoices.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save invoice</flux:button>
        </div>
    </form>
</div>
