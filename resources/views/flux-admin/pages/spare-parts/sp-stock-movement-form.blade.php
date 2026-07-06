<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.sp-stock-movements.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Stock movements</a>
                <span>/</span>
                <span>{{ $spStockMovement && $spStockMovement->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $spStockMovement && $spStockMovement->exists ? 'Edit movement' : 'New stock movement' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.sp-stock-movements.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flux-admin-form-grid grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-flux-admin::field-group label="Branch" required :error="$errors->first('form.branch_id')">
                    <flux:select wire:model="form.branch_id" placeholder="— Select —">
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Part" required :error="$errors->first('form.sp_part_id')">
                    <flux:select wire:model="form.sp_part_id" placeholder="— Select —">
                        @foreach($parts as $p)
                            <flux:select.option value="{{ $p->id }}">{{ $p->part_number }} · {{ $p->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date" required :error="$errors->first('form.transaction_date')">
                    <flux:input type="date" wire:model="form.transaction_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Type" required :error="$errors->first('form.transaction_type')">
                    <flux:select wire:model="form.transaction_type">
                        <flux:select.option value="purchase">Purchase</flux:select.option>
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="transfer">Transfer</flux:select.option>
                        <flux:select.option value="adjustment">Adjustment</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="In (qty)" :error="$errors->first('form.in')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.in" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Out (qty)" :error="$errors->first('form.out')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.out" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Ref doc" :error="$errors->first('form.ref_doc_no')">
                    <flux:input wire:model="form.ref_doc_no" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Remarks" :error="$errors->first('form.remarks')">
                    <flux:textarea wire:model="form.remarks" rows="2" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.sp-stock-movements.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
