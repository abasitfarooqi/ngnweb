<div>
    <x-flux-admin::data-table title="Rental pricing" description="Weekly hire price history per motorbike.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/renting-pricing/create')" class="!rounded-none">New price</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search reg, make or model…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.iscurrent" placeholder="Current">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Current</flux:select.option>
                        <flux:select.option value="0">Superseded</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Vehicle</flux:table.column>
                <flux:table.column>Weekly price</flux:table.column>
                <flux:table.column>Minimum deposit</flux:table.column>
                <flux:table.column>Current</flux:table.column>
                <flux:table.column>Effective</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="rp-{{ $r->id }}">
                        <flux:table.cell>
                            <div class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->motorbike?->make }} {{ $r->motorbike?->model }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->weekly_price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $r->minimum_deposit, 2) }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->iscurrent" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->update_date ? \Carbon\Carbon::parse($r->update_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/renting-pricing/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
