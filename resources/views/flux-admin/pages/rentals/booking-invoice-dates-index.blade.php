<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Booking invoice dates</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Edit the due date of any booking invoice inline.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="flux-admin-toolbar mb-6 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-stretch">
            <flux:input
                class="lg:flex-1"
                wire:model.live.debounce.300ms="search"
                placeholder="Search booking, invoice, customer, phone, reg…"
                variant="filled"
                icon="magnifying-glass"
            />
            <div class="lg:w-48">
                <flux:select wire:model.live="filters.paid" placeholder="Paid state">
                    <flux:select.option value="">All invoices</flux:select.option>
                    <flux:select.option value="unpaid">Unpaid only</flux:select.option>
                    <flux:select.option value="paid">Paid only</flux:select.option>
                </flux:select>
            </div>
            <div class="lg:w-32">
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="20">20 / page</flux:select.option>
                    <flux:select.option value="50">50 / page</flux:select.option>
                    <flux:select.option value="100">100 / page</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[60rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Booking</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Reg</flux:table.column>
                        <flux:table.column>Invoice</flux:table.column>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>State</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($rows as $r)
                            <flux:table.row wire:key="inv-{{ $r->invoice_id }}">
                                <flux:table.cell class="font-medium">#{{ $r->booking_id }}</flux:table.cell>
                                <flux:table.cell>
                                    {{ $r->first_name }} {{ $r->last_name }}
                                    <span class="block text-xs text-zinc-500">{{ $r->phone }}</span>
                                </flux:table.cell>
                                <flux:table.cell class="font-mono">{{ $r->reg_no ?: '—' }}</flux:table.cell>
                                <flux:table.cell>#{{ $r->invoice_id }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($editingId === (int) $r->invoice_id)
                                        <div class="flex items-center gap-2">
                                            <input type="date" wire:model="editingDate" class="border border-zinc-300 bg-white px-2 py-1 text-sm dark:border-zinc-700 dark:bg-zinc-800" />
                                        </div>
                                        @error('editingDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    @else
                                        {{ $r->invoice_date ? \Carbon\Carbon::parse($r->invoice_date)->format('d M Y') : '—' }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) $r->amount, 2) }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($r->is_paid)
                                        <flux:badge size="sm" color="emerald">Paid</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="amber">{{ $r->invoice_state ?: 'Pending' }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if ($editingId === (int) $r->invoice_id)
                                        <div class="flex gap-1">
                                            <flux:button size="sm" variant="primary" wire:click="saveEdit">Save</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="cancelEdit">Cancel</flux:button>
                                        </div>
                                    @else
                                        <flux:button size="sm" variant="ghost" icon="pencil" wire:click="startEdit({{ $r->invoice_id }}, '{{ $r->invoice_date }}')">Edit date</flux:button>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="py-8 text-center text-sm text-zinc-500">No invoices found.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
