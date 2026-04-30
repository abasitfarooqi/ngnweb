<div>
    <x-flux-admin::data-table title="Store front" description="Every product flagged for the Oxford or eCommerce storefront (with live branch stock).">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" icon="arrow-down-tray" :href="url(config('backpack.base.route_prefix').'/ngn-store-page/export-csv')" class="!rounded-none">Export CSV</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name or SKU…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_oxford" placeholder="Oxford">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Oxford only</flux:select.option>
                        <flux:select.option value="0">Not Oxford</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_ecommerce" placeholder="eCommerce">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">eCommerce only</flux:select.option>
                        <flux:select.option value="0">Not eCommerce</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortField === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column>SKU</flux:table.column>
                <flux:table.column>Brand</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Stock (all branches)</flux:table.column>
                <flux:table.column>Flags</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $p)
                    @php
                        $byBranch = $p->stockMovements->groupBy('branch_id')->map(function ($moves) {
                            return [
                                'branch' => optional($moves->first()->branch)->name,
                                'stock' => $moves->sum(fn ($m) => ((int) $m->in) - ((int) $m->out)),
                            ];
                        });
                        $total = $byBranch->sum('stock');
                    @endphp
                    <flux:table.row wire:key="sp-{{ $p->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ \Illuminate\Support\Str::limit($p->name, 60) }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $p->sku }}</flux:table.cell>
                        <flux:table.cell>{{ $p->brand?->name ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $p->category?->name ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">£{{ number_format((float) $p->normal_price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium">{{ $total }}</div>
                            <div class="text-xs text-zinc-500">{{ $byBranch->map(fn ($b) => ($b['branch'] ?: '—').': '.$b['stock'])->join(' · ') ?: 'No movements' }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                @if($p->is_oxford)<flux:badge size="xs" color="blue">Oxford</flux:badge>@endif
                                @if($p->is_ecommerce)<flux:badge size="xs" color="purple">eCom</flux:badge>@endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No storefront products.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
