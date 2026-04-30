<div>
    <x-flux-admin::data-table title="Contract extra items" description="Additional charges or fees attached to finance contracts.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New item</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name or application ID…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Application</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Qty</flux:table.column>
                <flux:table.column>Line total</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($items as $i)
                    <flux:table.row wire:key="cextra-{{ $i->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $i->id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">#{{ $i->application_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $i->application?->customer ? $i->application->customer->first_name.' '.$i->application->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $i->name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $i->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $i->quantity }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $i->price * (int) $i->quantity, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $i->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $i->id }})" wire:confirm="Delete this item?" icon="trash" class="!rounded-none text-red-600">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No items.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $items->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[28rem] !rounded-none">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $recordId ? 'Edit extra item' : 'New extra item' }}</flux:heading>
            </div>
            <form wire:submit="saveForm" class="space-y-4">
                <x-flux-admin::field-group label="Application ID" required :error="$errors->first('formData.application_id')">
                    <flux:input type="number" wire:model="formData.application_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Name" required :error="$errors->first('formData.name')">
                    <flux:input wire:model="formData.name" />
                </x-flux-admin::field-group>
                <div class="grid grid-cols-2 gap-3">
                    <x-flux-admin::field-group label="Price" required :error="$errors->first('formData.price')">
                        <flux:input type="number" step="0.01" wire:model="formData.price" />
                    </x-flux-admin::field-group>
                    <x-flux-admin::field-group label="Quantity" required :error="$errors->first('formData.quantity')">
                        <flux:input type="number" wire:model="formData.quantity" />
                    </x-flux-admin::field-group>
                </div>
                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" type="button" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                    <flux:button variant="primary" type="submit" class="!rounded-none">Save</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
