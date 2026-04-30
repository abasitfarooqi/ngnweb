<div>
    <x-flux-admin::data-table title="MOT checker subscribers" description="Public MOT reminder subscriptions by email.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">Add subscriber</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search VRM or email…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.horizon" placeholder="When">
                        <flux:select.option value="">Any time</flux:select.option>
                        <flux:select.option value="upcoming">Upcoming</flux:select.option>
                        <flux:select.option value="overdue">Overdue</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'mot_due_date'" :direction="$sortField === 'mot_due_date' ? $sortDirection : null" wire:click="sortBy('mot_due_date')">MOT due</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="mot-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->vehicle_registration }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white whitespace-nowrap">
                            {{ $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('d M Y') : '—' }}
                            @if($r->mot_due_date && \Carbon\Carbon::parse($r->mot_due_date)->isPast())
                                <span class="ml-1 text-xs text-red-600 dark:text-red-400">overdue</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->email }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Remove this subscriber?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No subscribers.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[520px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit subscriber' : 'Add subscriber' }}</flux:heading>
            <x-flux-admin::field-group label="Vehicle registration" :error="$errors->first('formData.vehicle_registration')" required>
                <flux:input wire:model="formData.vehicle_registration" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="MOT due date" :error="$errors->first('formData.mot_due_date')" required>
                <flux:input type="date" wire:model="formData.mot_due_date" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Email" :error="$errors->first('formData.email')" required>
                <flux:input type="email" wire:model="formData.email" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
