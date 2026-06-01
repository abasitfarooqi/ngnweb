<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.ec-orders.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">E-commerce orders</a>
                <span>/</span>
                <span>{{ $ecOrder && $ecOrder->exists ? 'Edit order #'.$ecOrder->id : 'New order' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $ecOrder && $ecOrder->exists ? 'Edit order #'.$ecOrder->id : 'New e-commerce order' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.ec-orders.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Order details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Order date" required :error="$errors->first('form.order_date')">
                    <flux:input type="date" wire:model="form.order_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer ID" :error="$errors->first('form.customer_id')">
                    <flux:input type="number" wire:model="form.customer_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch ID" :error="$errors->first('form.branch_id')">
                    <flux:input type="number" wire:model="form.branch_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Currency" :error="$errors->first('form.currency')">
                    <flux:input wire:model="form.currency" placeholder="GBP" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Order status" required :error="$errors->first('form.order_status')">
                    <flux:select wire:model="form.order_status" placeholder="— Select —">
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="confirmed">Confirmed</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment status" :error="$errors->first('form.payment_status')">
                    <flux:select wire:model="form.payment_status" placeholder="— Select —">
                        <flux:select.option value="unpaid">Unpaid</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="refunded">Refunded</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping status" :error="$errors->first('form.shipping_status')">
                    <flux:select wire:model="form.shipping_status" placeholder="— Select —">
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="shipped">Shipped</flux:select.option>
                        <flux:select.option value="delivered">Delivered</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment reference" :error="$errors->first('form.payment_reference')">
                    <flux:input wire:model="form.payment_reference" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total amount (£)" :error="$errors->first('form.total_amount')">
                    <flux:input type="number" step="0.01" wire:model="form.total_amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Discount (£)" :error="$errors->first('form.discount')">
                    <flux:input type="number" step="0.01" wire:model="form.discount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax (£)" :error="$errors->first('form.tax')">
                    <flux:input type="number" step="0.01" wire:model="form.tax" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Grand total (£)" :error="$errors->first('form.grand_total')">
                    <flux:input type="number" step="0.01" wire:model="form.grand_total" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping cost (£)" :error="$errors->first('form.shipping_cost')">
                    <flux:input type="number" step="0.01" wire:model="form.shipping_cost" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping date" :error="$errors->first('form.shipping_date')">
                    <flux:input type="date" wire:model="form.shipping_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment date" :error="$errors->first('form.payment_date')">
                    <flux:input type="date" wire:model="form.payment_date" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.ec-orders.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
