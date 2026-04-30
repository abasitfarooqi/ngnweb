<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">PCN Cases</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $cases->total() }} cases in total</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ url('/ngn-admin/pcn-case/create') }}" icon="plus" variant="primary">New PCN case</flux:button>
        </div>
    </div>

    {{-- Toolbar: no leading icon (avoids mis-sized icon rail); one row on lg, stacked on small screens. --}}
    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1 lg:min-w-[14rem]">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search PCN number, customer, registration…"
                    variant="filled"
                />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[11rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="status" placeholder="All statuses">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="open">Open</flux:select.option>
                        <flux:select.option value="closed">Closed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="isPolice" placeholder="Police filter">
                        <flux:select.option value="">All</flux:select.option>
                        <flux:select.option value="yes">Police only</flux:select.option>
                        <flux:select.option value="no">Non-police</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-36">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table: horizontal pan on small viewports; min width keeps columns readable. --}}
    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[56rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'pcn_number'" :direction="$sortField === 'pcn_number' ? $sortDirection : null" wire:click="sortBy('pcn_number')">PCN number</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Motorbike</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'date_of_contravention'" :direction="$sortField === 'date_of_contravention' ? $sortDirection : null" wire:click="sortBy('date_of_contravention')">Date</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'full_amount'" :direction="$sortField === 'full_amount' ? $sortDirection : null" wire:click="sortBy('full_amount')">Full amount</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'reduced_amount'" :direction="$sortField === 'reduced_amount' ? $sortDirection : null" wire:click="sortBy('reduced_amount')">Reduced</flux:table.column>
                        <flux:table.column>Police?</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'isClosed'" :direction="$sortField === 'isClosed' ? $sortDirection : null" wire:click="sortBy('isClosed')">Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($cases as $row)
                            <flux:table.row wire:key="pcn-{{ $row->id }}">
                                <flux:table.cell>
                                    <a href="{{ route('flux-admin.pcn.show', $row->id) }}" class="font-medium text-zinc-900 hover:underline dark:text-white">
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
                                <flux:table.cell class="py-8 text-center text-zinc-500 dark:text-zinc-400" colspan="8">
                                    No PCN cases found.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $cases->links() }}
    </div>
</div>
