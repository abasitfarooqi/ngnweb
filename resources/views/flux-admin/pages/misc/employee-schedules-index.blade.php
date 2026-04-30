<div>
    <x-flux-admin::data-table title="Employee schedules" description="Off-day assignments per staff member.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">Assign off day</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search employee name…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Off day</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="es-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->user ? trim(($r->user->first_name ?? '').' '.($r->user->last_name ?? '')) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->off_day }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Remove this assignment?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="3" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No schedules.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[480px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit assignment' : 'Assign off day' }}</flux:heading>
            <x-flux-admin::field-group label="Employee" :error="$errors->first('formData.user_id')" required>
                <flux:select wire:model="formData.user_id" placeholder="— Select —">
                    @foreach($users as $u)
                        <flux:select.option value="{{ $u->id }}">{{ trim(($u->first_name ?? '').' '.($u->last_name ?? '')) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Off day" :error="$errors->first('formData.off_day')" required>
                <flux:input type="date" wire:model="formData.off_day" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
