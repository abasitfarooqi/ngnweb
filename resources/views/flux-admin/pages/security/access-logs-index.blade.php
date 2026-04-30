<div>
    <x-flux-admin::data-table
        title="Access logs"
        description="Historical record of authentication and area-access attempts."
    >
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search IP, area or message…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="allowed">Allowed</flux:select.option>
                        <flux:select.option value="blocked">Blocked</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="date" wire:model.live="filters.from" placeholder="From" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="date" wire:model.live="filters.to" placeholder="To" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortField === 'created_at' ? $sortDirection : null" wire:click="sortBy('created_at')">When</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'ip_address'" :direction="$sortField === 'ip_address' ? $sortDirection : null" wire:click="sortBy('ip_address')">IP</flux:table.column>
                <flux:table.column>Area</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Message</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($logs as $log)
                    <flux:table.row wire:key="log-{{ $log->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            @if($log->user)
                                {{ trim(($log->user->first_name ?? '').' '.($log->user->last_name ?? '')) ?: $log->user->email }}
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $log->ip_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $log->area_attempted ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="$log->status" />
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-md truncate">{{ $log->message }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No access logs found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $logs->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
