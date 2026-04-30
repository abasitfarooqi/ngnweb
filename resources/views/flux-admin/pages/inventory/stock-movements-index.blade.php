<div>
    <x-flux-admin::data-table title="Stock movements" description="Audit log of stock in/out across branches.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New movement</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search ref doc or remarks…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.branch_id" placeholder="Branch">
                        <flux:select.option value="">All</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.transaction_type" placeholder="Type">
                        <flux:select.option value="">Any type</flux:select.option>
                        <flux:select.option value="purchase">Purchase</flux:select.option>
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="transfer">Transfer</flux:select.option>
                        <flux:select.option value="adjustment">Adjustment</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'transaction_date'" :direction="$sortField === 'transaction_date' ? $sortDirection : null" wire:click="sortBy('transaction_date')">Date</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Product</flux:table.column>
                <flux:table.column>In</flux:table.column>
                <flux:table.column>Out</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Ref</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="sm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $branches->firstWhere('id', $r->branch_id)?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->product_id }}</flux:table.cell>
                        <flux:table.cell class="text-emerald-600 dark:text-emerald-400">{{ $r->in ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-red-600 dark:text-red-400">{{ $r->out ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->transaction_type }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->ref_doc_no }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this movement?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[640px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit movement' : 'New stock movement' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')" required>
                    <flux:select wire:model="formData.branch_id" placeholder="— Select —">
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Product" :error="$errors->first('formData.product_id')" required>
                    <flux:select wire:model="formData.product_id" placeholder="— Select —">
                        @foreach($products as $p)
                            <flux:select.option value="{{ $p->id }}">{{ $p->sku }} · {{ $p->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date" :error="$errors->first('formData.transaction_date')" required>
                    <flux:input type="date" wire:model="formData.transaction_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Type" :error="$errors->first('formData.transaction_type')" required>
                    <flux:select wire:model="formData.transaction_type">
                        <flux:select.option value="purchase">Purchase</flux:select.option>
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="transfer">Transfer</flux:select.option>
                        <flux:select.option value="adjustment">Adjustment</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="In (qty)" :error="$errors->first('formData.in')">
                    <flux:input type="number" step="0.01" wire:model="formData.in" min="0" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Out (qty)" :error="$errors->first('formData.out')">
                    <flux:input type="number" step="0.01" wire:model="formData.out" min="0" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Ref doc" :error="$errors->first('formData.ref_doc_no')">
                <flux:input wire:model="formData.ref_doc_no" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Remarks" :error="$errors->first('formData.remarks')">
                <flux:textarea wire:model="formData.remarks" rows="2" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
