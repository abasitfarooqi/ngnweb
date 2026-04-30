<div>
    <x-flux-admin::data-table title="Digital invoices" description="PDF invoices issued for sales and services.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/ngn-digital-invoice/create')" class="!rounded-none">New invoice</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search invoice #, customer, email or reg…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="issued">Issued</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.invoice_type" placeholder="Type">
                        <flux:select.option value="">Any type</flux:select.option>
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="service">Service</flux:select.option>
                        <flux:select.option value="rental">Rental</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'issue_date'" :direction="$sortField === 'issue_date' ? $sortDirection : null" wire:click="sortBy('issue_date')">Issued</flux:table.column>
                <flux:table.column>Invoice #</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="di-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->issue_date ? \Carbon\Carbon::parse($r->issue_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->invoice_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->invoice_type }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white">{{ $r->customer_name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->customer_email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->registration_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->total, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">£{{ number_format((float) $r->total_paid, 2) }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/ngn-digital-invoice/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No invoices.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
