<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Payment Plan Applications</h1>
            <x-flux-admin::list-count :total="$applications->total()" :label="$listCountLabel" />
        </div>
        <div class="flex items-center gap-2">
            @if($exportable)
                <x-flux-admin::export-button />
            @endif
            <a href="{{ route('flux-admin.finance.create') }}" wire:navigate>
                <flux:button icon="plus" variant="primary" class="!rounded-none">New application</flux:button>
            </a>
        </div>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by ID, customer name, or reg no…" variant="filled" />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
            <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                <flux:select wire:model.live="contractType" placeholder="Contract type">
                    <flux:select.option value="">All Types</flux:select.option>
                    <flux:select.option value="is_new_latest">New Latest</flux:select.option>
                    <flux:select.option value="is_used_latest">Used Latest</flux:select.option>
                    <flux:select.option value="is_subscription">Subscription</flux:select.option>
                </flux:select>
            </div>
            <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                <flux:select wire:model.live="status" placeholder="Application status">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
            </div>
            <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                <flux:select wire:model.live="filterLogbook" placeholder="Log book">
                    <flux:select.option value="">All log books</flux:select.option>
                    <flux:select.option value="1">Sent</flux:select.option>
                    <flux:select.option value="0">Not sent</flux:select.option>
                </flux:select>
            </div>
            <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                <flux:select wire:model.live="filterPosted">
                    <flux:select.option value="1">Posted</flux:select.option>
                    <flux:select.option value="0">Not posted</flux:select.option>
                </flux:select>
            </div>
            <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                <flux:input type="date" wire:model.live="contractDateFrom" placeholder="Contract from" />
            </div>
            <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                <flux:input type="date" wire:model.live="contractDateTo" placeholder="Contract to" />
            </div>
            @if($this->hasActiveFinanceFilters())
                <div class="min-w-0 w-full sm:basis-full lg:basis-auto lg:w-auto flex items-stretch">
                    <flux:button wire:click="resetFinanceFilters" variant="ghost" size="sm" icon="x-mark" class="!rounded-none w-full sm:w-auto">
                        Reset filters
                    </flux:button>
                </div>
            @endif
            <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-28">
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="20">20 per page</flux:select.option>
                    <flux:select.option value="50">50 per page</flux:select.option>
                    <flux:select.option value="100">100 per page</flux:select.option>
                </flux:select>
            </div>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[52rem] md:min-w-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'id'" :direction="$sortDirection" wire:click="sortBy('id')">ID</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'contract_date'" :direction="$sortDirection" wire:click="sortBy('contract_date')">Contract Start Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Contract Type</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'deposit'" :direction="$sortDirection" wire:click="sortBy('deposit')">Deposit</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'weekly_instalment'" :direction="$sortDirection" wire:click="sortBy('weekly_instalment')">Monthly Instalment</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>Log Book</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($applications as $app)
                    <flux:table.row wire:key="fa-{{ $app->id }}">
                        <flux:table.cell class="font-mono text-xs">
                            <a href="{{ \App\Support\FluxAdminFinanceListQuery::showUrl($app) }}" class="text-blue-600 hover:underline dark:text-blue-400">{{ $app->id }}</a>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($app->customer)
                                {{ $app->customer->first_name }} {{ $app->customer->last_name }}
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $app->contract_date ? \Carbon\Carbon::parse($app->contract_date)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($app->is_cancelled)
                                <flux:badge color="red" size="sm">Cancelled</flux:badge>
                            @elseif($app->log_book_sent || $app->logbook_transfer_date)
                                <flux:badge color="blue" size="sm">Completed</flux:badge>
                            @else
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $type = match(true) {
                                    (bool) $app->is_new => 'New Motorcycle',
                                    (bool) $app->is_new_latest && (bool) $app->is_subscription => 'New Latest + Subscription',
                                    (bool) $app->is_used_latest && (bool) $app->is_subscription => 'Used Latest + Subscription',
                                    (bool) $app->is_subscription => 'Subscription',
                                    (bool) $app->is_new_latest => 'New Latest',
                                    (bool) $app->is_used_latest => 'Used Latest',
                                    (bool) $app->is_used_extended_custom => 'Used Ext. Custom',
                                    (bool) $app->is_used_extended => 'Used Extended',
                                    (bool) $app->is_used => 'Used',
                                    default => 'Unknown',
                                };
                            @endphp
                            <flux:badge color="zinc" size="sm">{{ $type }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>£{{ number_format($app->deposit ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>£{{ number_format($app->weekly_instalment ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($app->is_posted)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($app->log_book_sent)
                                <flux:badge color="green" size="sm">Sent</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ \App\Support\FluxAdminFinanceListQuery::editUrl($app) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <a href="{{ \App\Support\FluxAdminFinanceListQuery::showUrl($app) }}" wire:navigate class="inline-flex items-center gap-1 px-2 py-1 text-xs text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">View</a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $app->id }})" wire:confirm="Delete this application? This cannot be undone." icon="trash" class="!rounded-none text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No finance applications found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>

</div>
