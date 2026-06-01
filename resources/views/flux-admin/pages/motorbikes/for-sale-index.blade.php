<div>
    <x-flux-admin::data-table title="New motorbikes for sale" description="Catalogue of new bikes available for purchase.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.motorbike-for-sale.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New listing</flux:button></a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search make or model…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.type" placeholder="Type">
                        <flux:select.option value="">All types</flux:select.option>
                        <flux:select.option value="Scooter">Scooter</flux:select.option>
                        <flux:select.option value="Standard">Standard</flux:select.option>
                        <flux:select.option value="Super Sport">Super Sport</flux:select.option>
                        <flux:select.option value="Touring">Touring</flux:select.option>
                        <flux:select.option value="Other">Other</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Make</flux:table.column>
                <flux:table.column>Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Sale price</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bikes as $b)
                    <flux:table.row wire:key="for-sale-{{ $b->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $b->make }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->year }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->type }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->engine }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->colour }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $b->sale_new_price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.motorbike-for-sale.edit', $b->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $b->id }})" wire:confirm="Delete this listing?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $bikes->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[700px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit listing' : 'New listing' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Make" required :error="$errors->first('formData.make')">
                    <flux:input wire:model="formData.make" placeholder="e.g. Honda" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" required :error="$errors->first('formData.model')">
                    <flux:input wire:model="formData.model" placeholder="e.g. CBR500R" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <x-flux-admin::field-group label="Year" :error="$errors->first('formData.year')">
                    <flux:input wire:model="formData.year" placeholder="e.g. 2024" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('formData.colour')">
                    <flux:input wire:model="formData.colour" placeholder="e.g. Red" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Engine" :error="$errors->first('formData.engine')">
                    <flux:input wire:model="formData.engine" placeholder="e.g. 500cc" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Type" :error="$errors->first('formData.type')">
                    <flux:select wire:model="formData.type">
                        <flux:select.option value="">— select —</flux:select.option>
                        <flux:select.option value="manual">Manual</flux:select.option>
                        <flux:select.option value="automatic">Automatic</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Availability" :error="$errors->first('formData.availability')">
                    <flux:select wire:model="formData.availability">
                        <flux:select.option value="for sale">For sale</flux:select.option>
                        <flux:select.option value="reserved">Reserved</flux:select.option>
                        <flux:select.option value="sold">Sold</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Sale price (£)" :error="$errors->first('formData.sale_new_price')">
                <flux:input type="number" step="0.01" wire:model="formData.sale_new_price" placeholder="0.00" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Description" :error="$errors->first('formData.description')">
                <flux:textarea wire:model="formData.description" rows="3" />
            </x-flux-admin::field-group>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
