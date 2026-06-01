<div>
    <x-flux-admin::data-table title="Used vehicle purchases" description="Log of used motorbikes acquired from private sellers.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.used-purchases.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New purchase</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search seller, email, reg or phone…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'purchase_date'" :direction="$sortField === 'purchase_date' ? $sortDirection : null" wire:click="sortBy('purchase_date')">Date</flux:table.column>
                <flux:table.column>Seller</flux:table.column>
                <flux:table.column>Vehicle</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Mileage</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Outstanding</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="puv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->purchase_date ? \Carbon\Carbon::parse($r->purchase_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white">{{ $r->full_name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $r->phone_number }} · {{ $r->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year }} {{ $r->make }} {{ $r->model }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->current_mileage }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $r->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-amber-600 dark:text-amber-400">£{{ number_format((float) $r->outstanding, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.used-purchases.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $r->id }})" wire:confirm="Delete this record?" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[700px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit purchase' : 'New purchase' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Purchase date" :error="$errors->first('formData.purchase_date')">
                    <flux:input type="date" wire:model="formData.purchase_date" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Seller name" :error="$errors->first('formData.full_name')">
                    <flux:input wire:model="formData.full_name" placeholder="Full name" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('formData.phone_number')">
                    <flux:input wire:model="formData.phone_number" placeholder="Phone number" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input type="email" wire:model="formData.email" placeholder="Email address" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Postcode" :error="$errors->first('formData.postcode')">
                    <flux:input wire:model="formData.postcode" placeholder="Postcode" class="!rounded-none" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Address" :error="$errors->first('formData.address')">
                <flux:textarea wire:model="formData.address" placeholder="Address" rows="2" class="!rounded-none" />
            </x-flux-admin::field-group>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Make" :error="$errors->first('formData.make')">
                    <flux:input wire:model="formData.make" placeholder="Make" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Model" :error="$errors->first('formData.model')">
                    <flux:input wire:model="formData.model" placeholder="Model" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Year" :error="$errors->first('formData.year')">
                    <flux:input wire:model="formData.year" placeholder="Year" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Colour" :error="$errors->first('formData.colour')">
                    <flux:input wire:model="formData.colour" placeholder="Colour" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Reg No." :error="$errors->first('formData.reg_no')">
                    <flux:input wire:model="formData.reg_no" placeholder="Registration" class="!rounded-none uppercase" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="VIN" :error="$errors->first('formData.vin')">
                    <flux:input wire:model="formData.vin" placeholder="VIN" class="!rounded-none" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Price (£)" :error="$errors->first('formData.price')">
                    <flux:input type="number" step="0.01" wire:model="formData.price" placeholder="0.00" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Deposit (£)" :error="$errors->first('formData.deposit')">
                    <flux:input type="number" step="0.01" wire:model="formData.deposit" placeholder="0.00" class="!rounded-none" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Outstanding (£)" :error="$errors->first('formData.outstanding')">
                    <flux:input type="number" step="0.01" wire:model="formData.outstanding" placeholder="0.00" class="!rounded-none" />
                </x-flux-admin::field-group>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
