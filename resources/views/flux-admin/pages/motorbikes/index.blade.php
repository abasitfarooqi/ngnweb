<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Motorbikes</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $motorbikes->total() }} motorbikes in total</p>
        </div>
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
            <div class="min-w-[48rem] md:min-w-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'reg_no'" :direction="$sortField === 'reg_no' ? $sortDirection : null" wire:click="sortBy('reg_no')">Reg No</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'make'" :direction="$sortField === 'make' ? $sortDirection : null" wire:click="sortBy('make')">Make</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortField === 'model' ? $sortDirection : null" wire:click="sortBy('model')">Model</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'year'" :direction="$sortField === 'year' ? $sortDirection : null" wire:click="sortBy('year')">Year</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Profile</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($motorbikes as $row)
                    <flux:table.row wire:key="bike-{{ $row->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->reg_no }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->make }}</flux:table.cell>
                        <flux:table.cell>{{ $row->model }}</flux:table.cell>
                        <flux:table.cell>{{ $row->year }}</flux:table.cell>
                        <flux:table.cell>{{ $row->engine }}</flux:table.cell>
                        <flux:table.cell>{{ $row->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->vehicleProfile)
                                <flux:badge color="{{ $row->vehicleProfile->is_internal ? 'zinc' : 'blue' }}" size="sm">
                                    {{ $row->vehicleProfile->name }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No motorbikes found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $motorbikes->links() }}
    </div>
</div>
