<div>
    <x-flux-admin::data-table title="Repair updates" description="Individual jobs/line items attached to motorbike repairs.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New update</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search description or repair ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.motorbike_repair_id" placeholder="Repair ID" />
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Repair</flux:table.column>
                <flux:table.column>Job description</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="mru-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->motorbike_repair_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->job_description }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $r->price, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $r->note ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this update?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[640px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit update' : 'New repair update' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Motorbike repair ID" :error="$errors->first('formData.motorbike_repair_id')" required>
                    <flux:input type="number" wire:model="formData.motorbike_repair_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price (£)" :error="$errors->first('formData.price')" required>
                    <flux:input type="number" step="0.01" wire:model="formData.price" min="0" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Job description" :error="$errors->first('formData.job_description')" required>
                <flux:textarea wire:model="formData.job_description" rows="3" />
            </x-flux-admin::field-group>
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
