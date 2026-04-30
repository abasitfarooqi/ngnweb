<div>
    <x-flux-admin::data-table title="Spare parts · Fitments" description="Year, colour and country variants of each model.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New fitment</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search model…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:select wire:model.live="filters.is_active" placeholder="Active">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Inactive</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Make</flux:table.column>
                <flux:table.column>Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Country</flux:table.column>
                <flux:table.column>Active</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="spf-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->model?->make?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->model?->name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->colour_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->country_name }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_active" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this fitment?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[640px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit fitment' : 'New fitment' }}</flux:heading>
            <x-flux-admin::field-group label="Model" :error="$errors->first('formData.model_id')" required>
                <flux:select wire:model="formData.model_id" placeholder="— Select —">
                    @foreach($models as $m)
                        <flux:select.option value="{{ $m->id }}">{{ $m->make?->name }} · {{ $m->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Year" :error="$errors->first('formData.year')">
                    <flux:input type="number" wire:model="formData.year" min="1900" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('formData.colour_name')">
                    <flux:input wire:model="formData.colour_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Country" :error="$errors->first('formData.country_name')">
                    <flux:input wire:model="formData.country_name" />
                </x-flux-admin::field-group>
            </div>
            <flux:checkbox wire:model="formData.is_active" label="Active" />
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
