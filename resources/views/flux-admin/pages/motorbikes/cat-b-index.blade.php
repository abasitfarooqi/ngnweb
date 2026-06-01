<div>
    <x-flux-admin::data-table title="Category B motorbikes" description="Write-off / damaged motorbike log.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.motorbike-cat-b.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New Cat B entry</flux:button></a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search registration…" />
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column>Date of purchase</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="catb-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->motorbike?->make }} {{ $r->motorbike?->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->dop ? \Carbon\Carbon::parse($r->dop)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-sm truncate">{{ $r->notes }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.motorbike-cat-b.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit Cat B entry' : 'New Cat B entry' }}</flux:heading>

            <x-flux-admin::field-group label="Motorbike ID" :error="$errors->first('formData.motorbike_id')" required>
                <flux:input wire:model="formData.motorbike_id" type="number" placeholder="Enter motorbike ID" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Date of purchase" :error="$errors->first('formData.dop')">
                <flux:input wire:model="formData.dop" type="date" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')">
                <flux:select wire:model="formData.branch_id" placeholder="Select branch">
                    <flux:select.option value="">— None —</flux:select.option>
                    @foreach($branches as $branch)
                        <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>

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
