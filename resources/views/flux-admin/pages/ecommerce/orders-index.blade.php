<div>
    <x-flux-admin::data-table title="E-commerce orders" description="Online orders placed through the webshop.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/ec-order/create')" class="!rounded-none">New order</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer or email…">
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
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/ec-order/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No orders.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
