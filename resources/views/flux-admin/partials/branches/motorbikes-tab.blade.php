<div>
    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-stretch">
            <div class="min-w-0 w-full sm:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search registration, make, model…" variant="filled" />
            </div>
            <div class="w-full shrink-0 sm:w-36">
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="20">20 per page</flux:select.option>
                    <flux:select.option value="50">50 per page</flux:select.option>
                    <flux:select.option value="100">100 per page</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[32rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'reg_no'" :direction="$sortField === 'reg_no' ? $sortDirection : null" wire:click="sortBy('reg_no')">Reg No</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'make'" :direction="$sortField === 'make' ? $sortDirection : null" wire:click="sortBy('make')">Make</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortField === 'model' ? $sortDirection : null" wire:click="sortBy('model')">Model</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'year'" :direction="$sortField === 'year' ? $sortDirection : null" wire:click="sortBy('year')">Year</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($motorbikes as $row)
                            <flux:table.row wire:key="bike-{{ $row->id }}">
                                <flux:table.cell>
                                    <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" class="font-medium text-zinc-900 hover:underline dark:text-white">
                                        {{ $row->reg_no }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell>{{ $row->make }}</flux:table.cell>
                                <flux:table.cell>{{ $row->model }}</flux:table.cell>
                                <flux:table.cell>{{ $row->year }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell class="py-8 text-center text-zinc-500 dark:text-zinc-400" colspan="4">
                                    No motorbikes found for this branch.
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
