<div>
    <x-flux-admin::data-table title="Club member vehicles" description="Quickly verify & edit the VRM / make / model / year each club member has on file.">
        <x-slot:actions>
            <x-flux-admin::export-button />
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, phone, email, VRM…" />
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'full_name'" :direction="$sortField === 'full_name' ? $sortDirection : null" wire:click="sortBy('full_name')">Member</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Make</flux:table.column>
                <flux:table.column>Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cmv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">
                            <div>{{ $r->full_name ?: '—' }}</div>
                            <div class="text-xs text-zinc-500">{{ $r->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->phone ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono font-medium">{{ $r->vrm ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->make ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->model ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit vehicle</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No members.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[520px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">Edit vehicle details</flux:heading>
            <flux:text size="sm" class="text-zinc-500">Member: {{ $formData['full_name'] ?? '—' }}</flux:text>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="VRM" :error="$errors->first('formData.vrm')">
                    <flux:input wire:model="formData.vrm" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('formData.year')">
                    <flux:input type="number" wire:model="formData.year" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('formData.make')">
                    <flux:input wire:model="formData.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('formData.model')">
                    <flux:input wire:model="formData.model" />
                </x-flux-admin::field-group>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
