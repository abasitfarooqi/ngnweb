<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Change booking start date</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Shift a booking's start date inline. Use carefully — invoice dates are not recalculated here.</p>
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
                placeholder="Search booking, customer name, email or phone…"
                variant="filled"
                icon="magnifying-glass"
            />
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
            <div class="min-w-[56rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Booking</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Start date</flux:table.column>
                        <flux:table.column>Due date</flux:table.column>
                        <flux:table.column>State</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($rows as $r)
                            <flux:table.row wire:key="bk-{{ $r->booking_id }}">
                                <flux:table.cell class="font-medium">#{{ $r->booking_id }}</flux:table.cell>
                                <flux:table.cell>{{ $r->first_name }} {{ $r->last_name }}<span class="block text-xs text-zinc-500">{{ $r->phone }}</span></flux:table.cell>
                                <flux:table.cell>
                                    @if ($editingId === (int) $r->booking_id)
                                        <input type="date" wire:model="editingStart" class="border border-zinc-300 bg-white px-2 py-1 text-sm dark:border-zinc-700 dark:bg-zinc-800" />
                                        @error('editingStart') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    @else
                                        {{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '—' }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ $r->due_date ? \Carbon\Carbon::parse($r->due_date)->format('d M Y') : '—' }}</flux:table.cell>
                                <flux:table.cell><flux:badge size="sm" color="zinc">{{ $r->state ?: '—' }}</flux:badge></flux:table.cell>
                                <flux:table.cell>
                                    @if ($editingId === (int) $r->booking_id)
                                        <div class="flex gap-1">
                                            <flux:button size="sm" variant="primary" wire:click="saveEdit">Save</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="cancelEdit">Cancel</flux:button>
                                        </div>
                                    @else
                                        <flux:button size="sm" variant="ghost" icon="pencil" wire:click="startEdit({{ $r->booking_id }}, '{{ $r->start_date }}')">Edit start date</flux:button>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="py-8 text-center text-sm text-zinc-500">No bookings.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
