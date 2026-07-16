<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Total NGN Vehicles</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Internal NGN vehicles (profile 1) — {{ number_format($motorbikes->total()) }} shown
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($exportable)
                <x-flux-admin::export-button />
            @endif
            <a href="{{ route('flux-admin.motorbikes.index') }}" wire:navigate>
                <flux:button variant="ghost" class="!rounded-none">All motorbikes</flux:button>
            </a>
        </div>
    </div>

    <div class="flux-admin-segment-tabs mb-4 flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="$set('filterCategory', '')"
            data-segment="all"
            data-active="{{ $filterCategory === '' ? 'true' : 'false' }}"
        >All ({{ number_format($categoryCounts['total']) }})</button>
        <button
            type="button"
            wire:click="$set('filterCategory', 'rental')"
            data-segment="rental"
            data-active="{{ $filterCategory === 'rental' ? 'true' : 'false' }}"
        >Active rental ({{ number_format($categoryCounts['rental']) }})</button>
        <button
            type="button"
            wire:click="$set('filterCategory', 'finance_new')"
            data-segment="finance_new"
            data-active="{{ $filterCategory === 'finance_new' ? 'true' : 'false' }}"
        >Finance new ({{ number_format($categoryCounts['finance_new']) }})</button>
        <button
            type="button"
            wire:click="$set('filterCategory', 'finance_used')"
            data-segment="finance_used"
            data-active="{{ $filterCategory === 'finance_used' ? 'true' : 'false' }}"
        >Finance used ({{ number_format($categoryCounts['finance_used']) }})</button>
        <button
            type="button"
            wire:click="$set('filterCategory', 'company')"
            data-segment="company"
            data-active="{{ $filterCategory === 'company' ? 'true' : 'false' }}"
        >Company ({{ number_format($categoryCounts['company']) }})</button>
        <button
            type="button"
            wire:click="$set('filterCategory', 'sale_rental')"
            data-segment="sale_rental"
            data-active="{{ in_array($filterCategory, ['sale_rental', 'for_sale'], true) ? 'true' : 'false' }}"
        >Sale rental ({{ number_format($categoryCounts['sale_rental'] ?? $categoryCounts['for_sale'] ?? 0) }})</button>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search registration, make, model, VIN…" variant="filled" />
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="branch" placeholder="All branches">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filterMotStatus" placeholder="MOT status">
                        <flux:select.option value="">Any MOT status</flux:select.option>
                        <flux:select.option value="1">Has compliance record</flux:select.option>
                        <flux:select.option value="0">No compliance record</flux:select.option>
                    </flux:select>
                </div>

                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:input wire:model.live.debounce.300ms="filterColour" placeholder="Colour…" variant="filled" />
                </div>

                <div class="min-w-0 w-full sm:min-w-[7rem] sm:flex-1 lg:w-28 lg:flex-none">
                    <flux:input wire:model.live.debounce.300ms="filterYear" placeholder="Year e.g. 2021" variant="filled" type="number" min="1900" max="2100" />
                </div>

                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-32">
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
            <div class="min-w-[72rem] md:min-w-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'reg_no'" :direction="$sortField === 'reg_no' ? $sortDirection : null" wire:click="sortBy('reg_no')">Reg No</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'make'" :direction="$sortField === 'make' ? $sortDirection : null" wire:click="sortBy('make')">Make</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortField === 'model' ? $sortDirection : null" wire:click="sortBy('model')">Model</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'color'" :direction="$sortField === 'color' ? $sortDirection : null" wire:click="sortBy('color')">Colour</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'year'" :direction="$sortField === 'year' ? $sortDirection : null" wire:click="sortBy('year')">Year</flux:table.column>
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>MOT Due</flux:table.column>
                <flux:table.column>Road Tax Due</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($motorbikes as $row)
                    @php
                        $compliance = $row->latestCompliance;
                        $motDue = $compliance?->mot_due_date ? \Carbon\Carbon::parse($compliance->mot_due_date) : null;
                        $taxDue = $compliance?->tax_due_date ? \Carbon\Carbon::parse($compliance->tax_due_date) : null;
                        $motSoon = $motDue && $motDue->diffInDays(now(), false) >= -30 && $motDue->isFuture();
                        $taxSoon = $taxDue && $taxDue->diffInDays(now(), false) >= -30 && $taxDue->isFuture();
                        $motExpired = $motDue && $motDue->isPast();
                        $taxExpired = $taxDue && $taxDue->isPast();
                        $roles = $rolesMap[$row->id] ?? [];
                    @endphp
                    <flux:table.row wire:key="tv-bike-{{ $row->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->reg_no }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->make }}</flux:table.cell>
                        <flux:table.cell>{{ $row->model }}</flux:table.cell>
                        <flux:table.cell>{{ $row->color ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->year }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @forelse($roles as $role)
                                    <flux:badge
                                        size="sm"
                                        color="{{ match($role) {
                                            'Active rental' => 'green',
                                            'Finance new' => 'blue',
                                            'Finance used' => 'purple',
                                            'Company' => 'amber',
                                            'Sale rental' => 'cyan',
                                            'Internal fleet' => 'zinc',
                                            default => 'zinc',
                                        } }}"
                                    >{{ $role }}</flux:badge>
                                @empty
                                    <span class="text-zinc-400">—</span>
                                @endforelse
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($motDue)
                                <span @class([
                                    'text-red-600 dark:text-red-400 font-medium' => $motExpired,
                                    'text-amber-600 dark:text-amber-400 font-medium' => $motSoon && !$motExpired,
                                    'text-zinc-700 dark:text-zinc-300' => !$motSoon && !$motExpired,
                                ])>{{ $motDue->format('d M Y') }}</span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($taxDue)
                                <span @class([
                                    'text-red-600 dark:text-red-400 font-medium' => $taxExpired,
                                    'text-amber-600 dark:text-amber-400 font-medium' => $taxSoon && !$taxExpired,
                                    'text-zinc-700 dark:text-zinc-300' => !$taxSoon && !$taxExpired,
                                ])>{{ $taxDue->format('d M Y') }}</span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" wire:navigate>
                                <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none">View</flux:button>
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No vehicles match this overview.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $motorbikes->links() }}
    </div>
</div>
