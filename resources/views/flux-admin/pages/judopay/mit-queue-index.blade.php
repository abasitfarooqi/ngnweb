<div>
    <x-flux-admin::data-table title="Judopay MIT queue" description="Scheduled merchant-initiated transactions via Judopay.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/judopay-mit-queue/create')" class="!rounded-none">New entry</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search Judo payment reference…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.cleared" placeholder="Cleared">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Cleared</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.fired" placeholder="Fired">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Fired</flux:select.option>
                        <flux:select.option value="0">Not fired</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'mit_fire_date'" :direction="$sortField === 'mit_fire_date' ? $sortDirection : null" wire:click="sortBy('mit_fire_date')">Fire date</flux:table.column>
                <flux:table.column>Payment ref</flux:table.column>
                <flux:table.column>NGN MIT #</flux:table.column>
                <flux:table.column>Retry</flux:table.column>
                <flux:table.column>Fired</flux:table.column>
                <flux:table.column>Cleared</flux:table.column>
                <flux:table.column>Cleared at</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="jmq-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->mit_fire_date ? \Carbon\Carbon::parse($r->mit_fire_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->judopay_payment_reference }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->ngn_mit_queue_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->retry }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->fired" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->cleared" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->cleared_at?->format('d M H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/judopay-mit-queue/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
