<div>
    <x-flux-admin::data-table title="Spare parts · Assembly parts" description="Individual parts belonging to assemblies.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New entry</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search part # or name…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.assembly_id" placeholder="Assembly">
                        <flux:select.option value="">All assemblies</flux:select.option>
                        @foreach($assemblies as $a)
                            <flux:select.option value="{{ $a->id }}">{{ $a->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Assembly</flux:table.column>
                <flux:table.column>Part #</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Qty used</flux:table.column>
                <flux:table.column>Price override</flux:table.column>
                <flux:table.column>Stock override</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="spap-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->assembly?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->part?->part_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->part?->name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->qty_used }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->price_override !== null ? '£'.number_format((float) $r->price_override, 2) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->stock_override ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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
            <flux:heading size="lg">{{ $recordId ? 'Edit entry' : 'New entry' }}</flux:heading>
            <x-flux-admin::field-group label="Assembly" :error="$errors->first('formData.assembly_id')" required>
                <flux:select wire:model="formData.assembly_id" placeholder="— Select —">
                    @foreach($assemblies as $a)
                        <flux:select.option value="{{ $a->id }}">{{ $a->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Part" :error="$errors->first('formData.part_id')" required>
                <flux:select wire:model="formData.part_id" placeholder="— Select —">
                    @foreach($parts as $p)
                        <flux:select.option value="{{ $p->id }}">{{ $p->part_number }} · {{ $p->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Qty used" :error="$errors->first('formData.qty_used')" required>
                    <flux:input type="number" wire:model="formData.qty_used" min="1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sort order" :error="$errors->first('formData.sort_order')">
                    <flux:input type="number" wire:model="formData.sort_order" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Price override" :error="$errors->first('formData.price_override')">
                    <flux:input type="number" step="0.01" wire:model="formData.price_override" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Stock override" :error="$errors->first('formData.stock_override')">
                    <flux:input type="number" step="0.01" wire:model="formData.stock_override" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Note override" :error="$errors->first('formData.note_override')">
                <flux:textarea wire:model="formData.note_override" rows="2" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
