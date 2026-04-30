<div>
    <x-flux-admin::data-table title="Delivery order enquiries" description="Motorbike transport requests including pricing and pickup detail.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/motorbike-delivery-order-enquiries/create')" class="!rounded-none">New enquiry</flux:button>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search order, name, phone, email or VRM…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_dealt" placeholder="Dealt">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Dealt</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Order</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Pickup → Dropoff</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'pick_up_datetime'" :direction="$sortField === 'pick_up_datetime' ? $sortDirection : null" wire:click="sortBy('pick_up_datetime')">Pickup</flux:table.column>
                <flux:table.column>Distance</flux:table.column>
                <flux:table.column>Cost</flux:table.column>
                <flux:table.column>Dealt</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="dlv-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->order_id ?? $r->id }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white">{{ $r->full_name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->phone }} · {{ $r->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->vrm }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 text-xs">{{ $r->pickup_postcode }} → {{ $r->dropoff_postcode }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->pick_up_datetime ? \Carbon\Carbon::parse($r->pick_up_datetime)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->distance ? number_format((float) $r->distance, 1).' mi' : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->total_cost, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:switch :checked="(bool) $r->is_dealt" wire:click="toggleDealt({{ $r->id }})" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/motorbike-delivery-order-enquiries/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No enquiries.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
