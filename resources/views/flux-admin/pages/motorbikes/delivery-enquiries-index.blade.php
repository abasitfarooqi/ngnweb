<div>
    <x-flux-admin::data-table title="Delivery order enquiries" description="Motorbike transport requests including pricing and pickup detail.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.delivery-enquiries.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New enquiry</flux:button></a>
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
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.delivery-enquiries.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No enquiries.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit enquiry' : 'New enquiry' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Customer name" :error="$errors->first('formData.full_name')" required>
                    <flux:input wire:model="formData.full_name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('formData.phone')">
                    <flux:input wire:model="formData.phone" type="tel" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input wire:model="formData.email" type="email" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="VRM" :error="$errors->first('formData.vrm')">
                    <flux:input wire:model="formData.vrm" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Pickup postcode" :error="$errors->first('formData.pickup_postcode')">
                    <flux:input wire:model="formData.pickup_postcode" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Dropoff postcode" :error="$errors->first('formData.dropoff_postcode')">
                    <flux:input wire:model="formData.dropoff_postcode" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Pickup date / time" :error="$errors->first('formData.pick_up_datetime')">
                    <flux:input wire:model="formData.pick_up_datetime" type="datetime-local" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')">
                    <flux:select wire:model="formData.branch_id" placeholder="Select branch">
                        <flux:select.option value="">— None —</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Distance (mi)" :error="$errors->first('formData.distance')">
                    <flux:input wire:model="formData.distance" type="number" step="0.1" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Total cost (£)" :error="$errors->first('formData.total_cost')">
                    <flux:input wire:model="formData.total_cost" type="number" step="0.01" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Pickup address" :error="$errors->first('formData.pickup_address')">
                <flux:textarea wire:model="formData.pickup_address" rows="2" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Dropoff address" :error="$errors->first('formData.dropoff_address')">
                <flux:textarea wire:model="formData.dropoff_address" rows="2" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Notes" :error="$errors->first('formData.note')">
                <flux:textarea wire:model="formData.note" rows="2" />
            </x-flux-admin::field-group>

            <div class="flex items-center gap-2">
                <flux:checkbox wire:model="formData.is_dealt" id="is_dealt" />
                <label for="is_dealt" class="text-sm text-zinc-700 dark:text-zinc-300">Mark as dealt</label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
