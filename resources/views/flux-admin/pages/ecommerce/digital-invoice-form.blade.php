<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.digital-invoices.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Digital Invoices</a>
                <span>/</span>
                <span>{{ $digitalInvoice ? 'Edit' : 'New invoice' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $digitalInvoice ? 'Edit invoice: '.($digitalInvoice->invoice_number ?: '#'.$digitalInvoice->id) : 'New digital invoice' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.digital-invoices.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save invoice</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Invoice details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Invoice number" :error="$errors->first('form.invoice_number')">
                    <flux:input wire:model="form.invoice_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Invoice type" required :error="$errors->first('form.invoice_type')">
                    <flux:input wire:model="form.invoice_type" placeholder="e.g. sale, service" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('form.invoice_category')">
                    <flux:input wire:model="form.invoice_category" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="sent">Sent</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="overdue">Overdue</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Issue date" required :error="$errors->first('form.issue_date')">
                    <flux:input type="date" wire:model="form.issue_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Due date" :error="$errors->first('form.due_date')">
                    <flux:input type="date" wire:model="form.due_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Amount (£)" :error="$errors->first('form.amount')">
                    <flux:input type="number" step="0.01" wire:model="form.amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total paid (£)" :error="$errors->first('form.total_paid')">
                    <flux:input type="number" step="0.01" wire:model="form.total_paid" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Customer & vehicle</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer name" :error="$errors->first('form.customer_name')">
                    <flux:input wire:model="form.customer_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer email" :error="$errors->first('form.customer_email')">
                    <flux:input type="email" wire:model="form.customer_email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer phone" :error="$errors->first('form.customer_phone')">
                    <flux:input wire:model="form.customer_phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Registration number" :error="$errors->first('form.registration_number')">
                    <flux:input wire:model="form.registration_number" class="uppercase" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" min="1900" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VIN" :error="$errors->first('form.vin')">
                    <flux:input wire:model="form.vin" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Notes</h2>
            <div class="space-y-4">
                <x-flux-admin::field-group label="Customer notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Internal notes" :error="$errors->first('form.internal_notes')">
                    <flux:textarea wire:model="form.internal_notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.digital-invoices.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save invoice</flux:button>
        </div>
    </form>
</div>
