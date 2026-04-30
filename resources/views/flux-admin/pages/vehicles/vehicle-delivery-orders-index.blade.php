<div>
    <x-flux-admin::data-table title="Vehicle delivery orders" description="Car delivery bookings (separate from motorbike deliveries).">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New order</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, email, phone or VRM…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.delivery_vehicle_type_id" placeholder="Vehicle type">
                        <flux:select.option value="">All types</flux:select.option>
                        @foreach($types as $t)
                            <flux:select.option value="{{ $t->id }}">{{ $t->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'quote_date'" :direction="$sortField === 'quote_date' ? $sortDirection : null" wire:click="sortBy('quote_date')">Quote</flux:table.column>
                <flux:table.column>Pickup</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Distance</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="vdo-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->quote_date ? \Carbon\Carbon::parse($r->quote_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->pickup_date ? \Carbon\Carbon::parse($r->pickup_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->vrm ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->full_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 text-xs">
                            <div>{{ $r->email ?: '—' }}</div>
                            <div>{{ $r->phone_number ?: '—' }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->total_distance ? number_format((float) $r->total_distance, 1).' mi' : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this order?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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
            <flux:heading size="lg">{{ $recordId ? 'Edit order' : 'New vehicle delivery order' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Quote date" :error="$errors->first('formData.quote_date')" required>
                    <flux:input type="date" wire:model="formData.quote_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Pickup date" :error="$errors->first('formData.pickup_date')">
                    <flux:input type="date" wire:model="formData.pickup_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Vehicle type" :error="$errors->first('formData.delivery_vehicle_type_id')" required>
                    <flux:select wire:model="formData.delivery_vehicle_type_id" placeholder="— Select —">
                        @foreach($types as $t)
                            <flux:select.option value="{{ $t->id }}">{{ $t->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')">
                    <flux:select wire:model="formData.branch_id" placeholder="— Select —">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('formData.vrm')">
                    <flux:input wire:model="formData.vrm" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Full name" :error="$errors->first('formData.full_name')" required>
                    <flux:input wire:model="formData.full_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('formData.phone_number')">
                    <flux:input wire:model="formData.phone_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input type="email" wire:model="formData.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total distance (miles)" :error="$errors->first('formData.total_distance')">
                    <flux:input type="number" step="0.1" wire:model="formData.total_distance" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Surcharge (£)" :error="$errors->first('formData.surcharge')">
                    <flux:input type="number" step="0.01" wire:model="formData.surcharge" min="0" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Notes" :error="$errors->first('formData.notes')">
                <flux:textarea wire:model="formData.notes" rows="3" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
