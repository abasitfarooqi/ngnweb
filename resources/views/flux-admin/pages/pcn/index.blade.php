<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">PCN Cases</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                @if($status === 'open')
                    {{ number_format($cases->total()) }} open cases
                @elseif($status === 'closed')
                    {{ number_format($cases->total()) }} closed cases
                @else
                    {{ number_format($cases->total()) }} cases in total
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <flux:button wire:click="exportSummary" icon="table-cells" variant="ghost" size="sm" class="!rounded-none">Export summary</flux:button>
            <flux:button wire:click="exportWithUpdates" icon="arrow-down-tray" variant="ghost" size="sm" class="!rounded-none">Export with updates</flux:button>
            <a href="{{ route('flux-admin.pcn.create') }}" wire:navigate>
                <flux:button icon="plus" variant="primary" size="sm" class="!rounded-none">New PCN case</flux:button>
            </a>
        </div>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1 lg:min-w-[14rem]">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search PCN number, customer, email, registration…"
                    variant="filled"
                />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="status" placeholder="All statuses">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="open">Open</flux:select.option>
                        <flux:select.option value="closed">Closed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="isPolice" placeholder="Police filter">
                        <flux:select.option value="">All</flux:select.option>
                        <flux:select.option value="yes">Police only</flux:select.option>
                        <flux:select.option value="no">Non-police</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filterEverAppealed" placeholder="Ever appealed">
                        <flux:select.option value="">Ever appealed</flux:select.option>
                        <flux:select.option value="1">Appealed</flux:select.option>
                        <flux:select.option value="0">Not appealed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filterUpdateStatus" placeholder="Update status">
                        <flux:select.option value="">Update status</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="transferred">Transferred</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:input type="date" wire:model.live="filterDateFrom" placeholder="Date from" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:input type="date" wire:model.live="filterDateTo" placeholder="Date to" />
                </div>
                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-36">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
                <flux:button wire:click="resetPcnFilters" variant="ghost" size="sm" icon="x-mark" class="!rounded-none w-full sm:w-auto">
                    Reset filters
                </flux:button>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[88rem]">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'pcn_number'" :direction="$sortField === 'pcn_number' ? $sortDirection : null" wire:click="sortBy('pcn_number')">PCN no.</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'date_of_contravention'" :direction="$sortField === 'date_of_contravention' ? $sortDirection : null" wire:click="sortBy('date_of_contravention')">Date of contr.</flux:table.column>
                        <flux:table.column>Time</flux:table.column>
                        <flux:table.column>Elapsed</flux:table.column>
                        <flux:table.column>VRN</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Email</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'isClosed'" :direction="$sortField === 'isClosed' ? $sortDirection : null" wire:click="sortBy('isClosed')">Closed</flux:table.column>
                        <flux:table.column>Appealed</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'full_amount'" :direction="$sortField === 'full_amount' ? $sortDirection : null" wire:click="sortBy('full_amount')">Full</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'reduced_amount'" :direction="$sortField === 'reduced_amount' ? $sortDirection : null" wire:click="sortBy('reduced_amount')">Reduced</flux:table.column>
                        <flux:table.column>Updated by</flux:table.column>
                        <flux:table.column>Note</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortField === 'created_at' ? $sortDirection : null" wire:click="sortBy('created_at')">Created</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($cases as $row)
                            <flux:table.row wire:key="pcn-{{ $row->id }}">
                                <flux:table.cell>
                                    <a href="{{ route('flux-admin.pcn.show', $row->id) }}" class="font-medium text-zinc-900 hover:underline dark:text-white" wire:navigate>
                                        {{ $row->pcn_number }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">{{ $row->date_of_contravention?->format('d M Y') ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->time_of_contravention ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->getDaysSinceContravention() ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs">{{ $row->motorbike?->reg_no ?? '—' }}</flux:table.cell>
                                <flux:table.cell>{{ $row->customer?->first_name }} {{ $row->customer?->last_name }}</flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $row->customer?->email ?? '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$row->isOpen() ? 'green' : 'zinc'" size="sm">
                                        {{ $row->isOpen() ? 'Open' : 'Closed' }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($row->has_been_appealed)
                                        <flux:badge color="purple" size="sm">Yes</flux:badge>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500">No</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>£{{ number_format($row->full_amount ?? 0, 2) }}</flux:table.cell>
                                <flux:table.cell>£{{ number_format($row->reduced_amount ?? 0, 2) }}</flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $row->user?->first_name ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="max-w-[10rem] truncate text-xs text-zinc-500 dark:text-zinc-400" title="{{ $row->note }}">{{ $row->note ?: '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-xs whitespace-nowrap">{{ $row->created_at?->format('d M Y') ?? '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex gap-1">
                                        <a href="{{ route('flux-admin.pcn.edit', $row->id) }}" wire:navigate>
                                            <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                        </a>
                                        <flux:button size="xs" variant="danger" wire:click="delete({{ $row->id }})" wire:confirm="Delete this PCN case?" icon="trash" class="!rounded-none">Delete</flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell class="py-8 text-center text-zinc-500 dark:text-zinc-400" colspan="15">
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
