<div>
    <x-flux-admin::data-table title="NGN MIT queue" description="Upcoming scheduled recurring billing runs.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.ngn-mit-queue.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New entry</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search invoice #…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="processing">Processing</flux:select.option>
                        <flux:select.option value="success">Success</flux:select.option>
                        <flux:select.option value="failed">Failed</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.cleared" placeholder="Cleared">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Cleared</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'mit_fire_date'" :direction="$sortField === 'mit_fire_date' ? $sortDirection : null" wire:click="sortBy('mit_fire_date')">Fire date</flux:table.column>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>Invoice date</flux:table.column>
                <flux:table.column>Attempt</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Cleared</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="nmq-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->mit_fire_date ? \Carbon\Carbon::parse($r->mit_fire_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->invoice_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->invoice_date ? \Carbon\Carbon::parse($r->invoice_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->mit_attempt }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->cleared" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.ngn-mit-queue.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this queue entry?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                                @if(!$r->is_in_live_chamber && in_array($r->status, ['pending', 'failed']))
                                    <flux:button size="xs" variant="ghost" wire:click="addToQueue({{ $r->id }})" wire:confirm="Add this item to the live firing chamber?" icon="play" class="!rounded-none text-green-600 dark:text-green-400">Add to queue</flux:button>
                                @endif
                                @if($r->is_in_live_chamber && $r->live_chamber_item_id)
                                    <flux:button size="xs" variant="ghost" wire:click="stopQueue({{ $r->live_chamber_item_id }})" wire:confirm="Stop and remove this item from the live chamber?" icon="stop" class="!rounded-none text-red-600 dark:text-red-400">Stop</flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

</div>
