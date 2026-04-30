<div>
    <x-flux-admin::data-table title="DS order items" description="Pickup and drop-off legs for delivery service orders.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New leg</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM, postcode or order…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.ds_order_id" placeholder="Order ID" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Order</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Pickup</flux:table.column>
                <flux:table.column>Drop-off</flux:table.column>
                <flux:table.column>Distance</flux:table.column>
                <flux:table.column>Flags</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="dsi-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->ds_order_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->vrm ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-[16rem] truncate"><span class="font-mono text-xs">{{ $r->pickup_postcode }}</span> · {{ $r->pickup_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-[16rem] truncate"><span class="font-mono text-xs">{{ $r->dropoff_postcode }}</span> · {{ $r->dropoff_address }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->distance ? number_format((float) $r->distance, 1).' mi' : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-xs">
                            @if($r->moveable)<span class="mr-1 text-emerald-600 dark:text-emerald-400">Moveable</span>@endif
                            @if($r->keys)<span class="mr-1 text-emerald-600 dark:text-emerald-400">Keys</span>@endif
                            @if($r->documents)<span class="text-emerald-600 dark:text-emerald-400">Docs</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this leg?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit leg' : 'New delivery leg' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="DS order ID" :error="$errors->first('formData.ds_order_id')" required>
                    <flux:input type="number" wire:model="formData.ds_order_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('formData.vrm')">
                    <flux:input wire:model="formData.vrm" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup address" :error="$errors->first('formData.pickup_address')" required>
                    <flux:input wire:model="formData.pickup_address" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup postcode" :error="$errors->first('formData.pickup_postcode')" required>
                    <flux:input wire:model="formData.pickup_postcode" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Drop-off address" :error="$errors->first('formData.dropoff_address')" required>
                    <flux:input wire:model="formData.dropoff_address" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Drop-off postcode" :error="$errors->first('formData.dropoff_postcode')" required>
                    <flux:input wire:model="formData.dropoff_postcode" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup lat">
                    <flux:input type="number" step="0.000001" wire:model="formData.pickup_lat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup lon">
                    <flux:input type="number" step="0.000001" wire:model="formData.pickup_lon" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Drop-off lat">
                    <flux:input type="number" step="0.000001" wire:model="formData.dropoff_lat" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Drop-off lon">
                    <flux:input type="number" step="0.000001" wire:model="formData.dropoff_lon" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Distance (miles)">
                    <flux:input type="number" step="0.1" wire:model="formData.distance" />
                </x-flux-admin::field-group>
            </div>
            <div class="flex flex-wrap gap-4">
                <flux:checkbox wire:model="formData.moveable" label="Moveable" />
                <flux:checkbox wire:model="formData.documents" label="Has documents" />
                <flux:checkbox wire:model="formData.keys" label="Has keys" />
            </div>
            <x-flux-admin::field-group label="Note" :error="$errors->first('formData.note')">
                <flux:textarea wire:model="formData.note" rows="2" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
