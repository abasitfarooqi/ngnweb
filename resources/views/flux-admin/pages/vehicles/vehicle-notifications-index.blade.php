<div>
    <x-flux-admin::data-table title="Vehicle notifications" description="Customer requests to be notified when a vehicle becomes available.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.vehicle-notifications.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New subscription</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, email or registration…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.enable" placeholder="Enabled">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Active</flux:select.option>
                        <flux:select.option value="0">Disabled</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Email notify</flux:table.column>
                <flux:table.column>Phone notify</flux:table.column>
                <flux:table.column>Active</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="vn-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->first_name }} {{ $r->last_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->phone }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->reg_no }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->notify_email" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->notify_phone" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->enable" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.vehicle-notifications.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this subscription?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit subscription' : 'New subscription' }}</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="First name" :error="$errors->first('formData.first_name')" required>
                    <flux:input wire:model="formData.first_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Last name" :error="$errors->first('formData.last_name')" required>
                    <flux:input wire:model="formData.last_name" />
                </x-flux-admin::field-group>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input type="email" wire:model="formData.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('formData.phone')">
                    <flux:input wire:model="formData.phone" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Registration" :error="$errors->first('formData.reg_no')">
                <flux:input wire:model="formData.reg_no" />
            </x-flux-admin::field-group>
            <div class="flex flex-wrap gap-4">
                <flux:checkbox wire:model="formData.notify_email" label="Notify by email" />
                <flux:checkbox wire:model="formData.notify_phone" label="Notify by phone" />
                <flux:checkbox wire:model="formData.enable" label="Active" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
