<div>
    <x-flux-admin::data-table title="Partners" description="Third-party fleet operators and corporate partners.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New partner</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search company, name, email or phone…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_approved" placeholder="Approved">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Approved</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Company</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Fleet</flux:table.column>
                <flux:table.column>Approved</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="pa-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->companyname }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->first_name }} {{ $r->last_name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->phone }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->fleet_size }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_approved" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this partner?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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
            <flux:heading size="lg">{{ $recordId ? 'Edit partner' : 'New partner' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Company name" :error="$errors->first('formData.companyname')" required>
                    <flux:input wire:model="formData.companyname" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Company number" :error="$errors->first('formData.company_number')">
                    <flux:input wire:model="formData.company_number" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Company address" :error="$errors->first('formData.company_address')">
                <flux:textarea wire:model="formData.company_address" rows="2" />
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="First name" :error="$errors->first('formData.first_name')">
                    <flux:input wire:model="formData.first_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Last name" :error="$errors->first('formData.last_name')">
                    <flux:input wire:model="formData.last_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')">
                    <flux:input type="email" wire:model="formData.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Website" :error="$errors->first('formData.website')">
                    <flux:input wire:model="formData.website" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('formData.phone')">
                    <flux:input wire:model="formData.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Mobile" :error="$errors->first('formData.mobile')">
                    <flux:input wire:model="formData.mobile" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Fleet size" :error="$errors->first('formData.fleet_size')">
                    <flux:input type="number" wire:model="formData.fleet_size" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Operating since" :error="$errors->first('formData.operating_since')">
                    <flux:input type="date" wire:model="formData.operating_since" />
                </x-flux-admin::field-group>
            </div>
            <flux:checkbox wire:model="formData.is_approved" label="Approved" />
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
