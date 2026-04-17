<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Club Members</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $members->total() }} members in total</p>
        </div>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search name, email, phone, VRM…" variant="filled" />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="flex h-10 min-w-0 shrink-0 items-center gap-2 sm:h-auto sm:min-h-[2.5rem] lg:h-10">
                    <flux:switch wire:model.live="activeOnly" />
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active only</span>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:max-w-[11rem] lg:w-36">
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
                <flux:table.column sortable :sorted="$sortField === 'full_name'" :direction="$sortField === 'full_name' ? $sortDirection : null" wire:click="sortBy('full_name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortField === 'email' ? $sortDirection : null" wire:click="sortBy('email')">Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Vehicle</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'vrm'" :direction="$sortField === 'vrm' ? $sortDirection : null" wire:click="sortBy('vrm')">VRM</flux:table.column>
                <flux:table.column>Partner</flux:table.column>
                <flux:table.column>Active?</flux:table.column>
                <flux:table.column>Redeemable</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($members as $row)
                    <flux:table.row wire:key="club-{{ $row->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.club.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->full_name }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->email ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->phone ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ trim($row->make . ' ' . $row->model . ' ' . $row->year) ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->vrm ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->partner?->companyname ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$row->is_active ? 'green' : 'zinc'" size="sm">
                                {{ $row->is_active ? 'Yes' : 'No' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">£{{ number_format($row->available_redeemable_balance, 2) }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No club members found.
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
        {{ $members->links() }}
    </div>
</div>
