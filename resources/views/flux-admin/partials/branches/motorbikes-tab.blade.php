<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search reg no, make, model…" icon="magnifying-glass" />
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

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
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
                            <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->reg_no }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->make }}</flux:table.cell>
                        <flux:table.cell>{{ $row->model }}</flux:table.cell>
                        <flux:table.cell>{{ $row->year }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No motorbikes found for this branch.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div class="mt-4">
        {{ $motorbikes->links() }}
    </div>
</div>
