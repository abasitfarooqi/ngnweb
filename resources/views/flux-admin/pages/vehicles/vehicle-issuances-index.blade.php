<div>
    <x-flux-admin::data-table title="Vehicle issuances" description="Record of motorbikes handed to customers for test ride or inspection.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.vehicle-issuances.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New issuance</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer or registration…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_returned" placeholder="Returned">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Returned</flux:select.option>
                        <flux:select.option value="0">Outstanding</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Issued</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Returned</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="vi-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->issue_date ? \Carbon\Carbon::parse($r->issue_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_returned" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.vehicle-issuances.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this issuance record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit issuance' : 'New issuance' }}</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Issue date" :error="$errors->first('formData.issue_date')" required>
                    <flux:input type="date" wire:model="formData.issue_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="User ID" :error="$errors->first('formData.user_id')" required>
                    <flux:input type="number" wire:model="formData.user_id" />
                </x-flux-admin::field-group>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Motorbike ID" :error="$errors->first('formData.motorbike_id')" required>
                    <flux:input type="number" wire:model="formData.motorbike_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer ID" :error="$errors->first('formData.customer_id')">
                    <flux:input type="number" wire:model="formData.customer_id" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Branch ID" :error="$errors->first('formData.branch_id')">
                <flux:input type="number" wire:model="formData.branch_id" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Notes" :error="$errors->first('formData.notes')">
                <flux:textarea wire:model="formData.notes" rows="3" />
            </x-flux-admin::field-group>
            <flux:checkbox wire:model="formData.is_returned" label="Returned" />
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
