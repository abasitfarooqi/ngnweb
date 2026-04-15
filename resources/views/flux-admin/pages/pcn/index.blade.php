<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">PCN Cases</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $cases->total() }} cases in total</p>
        </div>
    </div>

    {{-- Filters bar --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search PCN number, customer, reg no…" icon="magnifying-glass" />
            </div>
            <div class="w-full sm:w-40">
                <flux:select wire:model.live="status" placeholder="All Statuses">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    <flux:select.option value="open">Open</flux:select.option>
                    <flux:select.option value="closed">Closed</flux:select.option>
                </flux:select>
            </div>
            <div class="w-full sm:w-40">
                <flux:select wire:model.live="isPolice" placeholder="Police?">
                    <flux:select.option value="">All</flux:select.option>
                    <flux:select.option value="yes">Police Only</flux:select.option>
                    <flux:select.option value="no">Non-Police</flux:select.option>
                </flux:select>
            </div>
            <div class="w-full sm:w-32">
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="20">20 per page</flux:select.option>
                    <flux:select.option value="50">50 per page</flux:select.option>
                    <flux:select.option value="100">100 per page</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'pcn_number'" :direction="$sortField === 'pcn_number' ? $sortDirection : null" wire:click="sortBy('pcn_number')">PCN Number</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'date_of_contravention'" :direction="$sortField === 'date_of_contravention' ? $sortDirection : null" wire:click="sortBy('date_of_contravention')">Date</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'full_amount'" :direction="$sortField === 'full_amount' ? $sortDirection : null" wire:click="sortBy('full_amount')">Full Amount</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'reduced_amount'" :direction="$sortField === 'reduced_amount' ? $sortDirection : null" wire:click="sortBy('reduced_amount')">Reduced</flux:table.column>
                <flux:table.column>Police?</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'isClosed'" :direction="$sortField === 'isClosed' ? $sortDirection : null" wire:click="sortBy('isClosed')">Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($cases as $row)
                    <flux:table.row wire:key="pcn-{{ $row->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.pcn.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->pcn_number }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->customer?->first_name }} {{ $row->customer?->last_name }}</flux:table.cell>
                        <flux:table.cell>{{ $row->motorbike?->reg_no ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->date_of_contravention?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($row->full_amount ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($row->reduced_amount ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_police)
                                <flux:badge color="red" size="sm">Police</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$row->isClosed ? 'zinc' : 'green'" size="sm">
                                {{ $row->isClosed ? 'Closed' : 'Open' }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No PCN cases found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $cases->links() }}
    </div>
</div>
