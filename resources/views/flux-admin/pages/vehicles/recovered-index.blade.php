<div>
    <x-flux-admin::data-table title="Recovered motorbikes" description="Motorbikes recovered from police, council or abandonment.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.recovered-motorbikes.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New entry</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search registration…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'case_date'" :direction="$sortField === 'case_date' ? $sortDirection : null" wire:click="sortBy('case_date')">Case date</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Returned</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="rm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->case_date ? \Carbon\Carbon::parse($r->case_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->motorbike?->make }} {{ $r->motorbike?->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->returned_date ? \Carbon\Carbon::parse($r->returned_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $r->notes }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <a href="{{ route('flux-admin.recovered-motorbikes.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this recovery record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit recovery record' : 'New recovery record' }}</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Case date" :error="$errors->first('formData.case_date')" required>
                    <flux:input type="date" wire:model="formData.case_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike ID" :error="$errors->first('formData.motorbike_id')" required>
                    <flux:input type="number" wire:model="formData.motorbike_id" />
                </x-flux-admin::field-group>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Branch ID" :error="$errors->first('formData.branch_id')">
                    <flux:input type="number" wire:model="formData.branch_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Returned date" :error="$errors->first('formData.returned_date')">
                    <flux:input type="date" wire:model="formData.returned_date" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Notes" :error="$errors->first('formData.notes')">
                <flux:textarea wire:model="formData.notes" rows="3" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
