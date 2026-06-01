<div>
    <x-flux-admin::data-table title="Finance application items" description="Motorbikes allocated to finance applications.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.application-items.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New item</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search app, registration or customer…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_posted" placeholder="Posted">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Posted</flux:select.option>
                        <flux:select.option value="0">Draft</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>App</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($items as $i)
                    <flux:table.row wire:key="app-item-{{ $i->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $i->id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $i->app_id ?: '#'.$i->application_id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $i->application?->customer ? $i->application->customer->first_name.' '.$i->application->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $i->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $i->motorbike ? $i->motorbike->make.' '.$i->motorbike->model : '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $i->is_posted" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.application-items.edit', $i->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $i->id }})" wire:confirm="Delete this application item?" icon="trash" class="!rounded-none">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No items.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $items->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit application item' : 'New application item' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Application ID" required :error="$errors->first('formData.application_id')">
                    <flux:input type="number" wire:model="formData.application_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike ID" required :error="$errors->first('formData.motorbike_id')">
                    <flux:input type="number" wire:model="formData.motorbike_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Start date" :error="$errors->first('formData.start_date')">
                    <flux:input type="date" wire:model="formData.start_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Due date" :error="$errors->first('formData.due_date')">
                    <flux:input type="date" wire:model="formData.due_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="End date" :error="$errors->first('formData.end_date')">
                    <flux:input type="date" wire:model="formData.end_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Weekly instalment (£)" :error="$errors->first('formData.weekly_instalment')">
                    <flux:input type="number" step="0.01" min="0" wire:model="formData.weekly_instalment" />
                </x-flux-admin::field-group>
            </div>

            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="formData.is_posted" class="accent-zinc-900 dark:accent-zinc-200"> Mark as posted
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
