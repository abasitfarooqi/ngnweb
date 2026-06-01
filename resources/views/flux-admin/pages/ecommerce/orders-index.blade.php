<div>
    <x-flux-admin::data-table title="E-commerce orders" description="Online orders placed through the webshop.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.ec-orders.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New order</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer or email…">
                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-28 lg:flex-none">
                    <flux:input wire:model.live.debounce.400ms="filterOrderId" placeholder="Order #…" variant="filled" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.order_status" placeholder="Order">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="confirmed">Confirmed</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.payment_status" placeholder="Payment">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="unpaid">Unpaid</flux:select.option>
                        <flux:select.option value="refunded">Refunded</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.shipping_status" placeholder="Shipping">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="shipped">Shipped</flux:select.option>
                        <flux:select.option value="delivered">Delivered</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'order_date'" :direction="$sortField === 'order_date' ? $sortDirection : null" wire:click="sortBy('order_date')">Date</flux:table.column>
                <flux:table.column>Order #</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Order</flux:table.column>
                <flux:table.column>Payment</flux:table.column>
                <flux:table.column>Shipping</flux:table.column>
                <flux:table.column>Ship. method</flux:table.column>
                <flux:table.column>Line types</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="eo-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->order_date ? \Carbon\Carbon::parse($r->order_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">#{{ $r->id }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->customer?->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ strtoupper($r->currency ?? 'GBP') }} {{ number_format((float) $r->grand_total, 2) }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->order_status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->payment_status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->shipping_status" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->shippingMethod?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 text-xs">
                            {{ $r->orderItems->pluck('item_type')->filter()->unique()->map(fn ($t) => strtoupper((string) $t))->implode(', ') ?: 'CATALOGUE' }}
                        </flux:table.cell>
                        <flux:table.cell class="flex gap-1">
                            <a href="{{ route('flux-admin.ec-orders.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                            <flux:button size="xs" variant="danger" wire:click="delete({{ $r->id }})" wire:confirm="Delete this record?" icon="trash" class="!rounded-none">Delete</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="11" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No orders.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    {{-- Modal removed: use dedicated form pages /ec-orders/create and /ec-orders/{id}/edit --}}
    <flux:modal wire:model.self="showForm" class="md:w-[760px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit order' : 'New order' }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Order date" :error="$errors->first('formData.order_date')" required>
                    <flux:input type="date" wire:model="formData.order_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer ID" :error="$errors->first('formData.customer_id')">
                    <flux:input type="number" wire:model="formData.customer_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch ID" :error="$errors->first('formData.branch_id')">
                    <flux:input type="number" wire:model="formData.branch_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Currency" :error="$errors->first('formData.currency')">
                    <flux:input wire:model="formData.currency" placeholder="GBP" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Order status" :error="$errors->first('formData.order_status')" required>
                    <flux:select wire:model="formData.order_status" placeholder="— Select —">
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="confirmed">Confirmed</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment status" :error="$errors->first('formData.payment_status')">
                    <flux:select wire:model="formData.payment_status" placeholder="— Select —">
                        <flux:select.option value="unpaid">Unpaid</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="refunded">Refunded</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping status" :error="$errors->first('formData.shipping_status')">
                    <flux:select wire:model="formData.shipping_status" placeholder="— Select —">
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="shipped">Shipped</flux:select.option>
                        <flux:select.option value="delivered">Delivered</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment reference" :error="$errors->first('formData.payment_reference')">
                    <flux:input wire:model="formData.payment_reference" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total amount" :error="$errors->first('formData.total_amount')">
                    <flux:input type="number" step="0.01" wire:model="formData.total_amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Discount" :error="$errors->first('formData.discount')">
                    <flux:input type="number" step="0.01" wire:model="formData.discount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax" :error="$errors->first('formData.tax')">
                    <flux:input type="number" step="0.01" wire:model="formData.tax" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Grand total" :error="$errors->first('formData.grand_total')">
                    <flux:input type="number" step="0.01" wire:model="formData.grand_total" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping cost" :error="$errors->first('formData.shipping_cost')">
                    <flux:input type="number" step="0.01" wire:model="formData.shipping_cost" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Shipping date" :error="$errors->first('formData.shipping_date')">
                    <flux:input type="date" wire:model="formData.shipping_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment date" :error="$errors->first('formData.payment_date')">
                    <flux:input type="date" wire:model="formData.payment_date" />
                </x-flux-admin::field-group>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
