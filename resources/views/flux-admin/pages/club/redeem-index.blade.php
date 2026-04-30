<div>
    <x-flux-admin::data-table title="Club redemptions" description="Points redemption log against POS invoices.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">Log redemption</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search POS invoice or member ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'date'" :direction="$sortField === 'date' ? $sortDirection : null" wire:click="sortBy('date')">Date</flux:table.column>
                <flux:table.column>Member</flux:table.column>
                <flux:table.column>POS invoice</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cmr-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">#{{ $r->club_member_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pos_invoice ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white font-semibold">£{{ number_format((float) $r->redeem_total, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $branches->firstWhere('id', $r->branch_id)?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this redemption?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[560px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit redemption' : 'Log redemption' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Club member ID" :error="$errors->first('formData.club_member_id')" required>
                    <flux:input type="number" wire:model="formData.club_member_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date" :error="$errors->first('formData.date')" required>
                    <flux:input type="date" wire:model="formData.date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Redeem total (£)" :error="$errors->first('formData.redeem_total')" required>
                    <flux:input type="number" step="0.01" wire:model="formData.redeem_total" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS invoice" :error="$errors->first('formData.pos_invoice')">
                    <flux:input wire:model="formData.pos_invoice" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')">
                    <flux:select wire:model="formData.branch_id" placeholder="— Select —">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Note" :error="$errors->first('formData.note')">
                <flux:textarea wire:model="formData.note" rows="2" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
