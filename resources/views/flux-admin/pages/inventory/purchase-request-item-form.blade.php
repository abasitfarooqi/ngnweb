<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.purchase-request-items.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Purchase Request Items</a>
                <span>/</span>
                <span>{{ $purchaseRequestItem ? 'Edit' : 'New item' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $purchaseRequestItem ? 'Edit item #'.$purchaseRequestItem->id : 'New purchase request item' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.purchase-request-items.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save item</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Item details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Purchase request" required :error="$errors->first('form.pr_id')">
                    <flux:select wire:model="form.pr_id" placeholder="Select request">
                        <flux:select.option value="">Select…</flux:select.option>
                        @foreach($purchaseRequests as $pr)
                            <flux:select.option value="{{ $pr->id }}">#{{ $pr->id }} — {{ $pr->date }} {{ $pr->note ? '('.$pr->note.')' : '' }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Part number" :error="$errors->first('form.part_number')">
                    <flux:input wire:model="form.part_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Part position" :error="$errors->first('form.part_position')">
                    <flux:input wire:model="form.part_position" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Brand" :error="$errors->first('form.brand_name_id')">
                    <flux:select wire:model="form.brand_name_id" placeholder="Select brand">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($makes as $make)
                            <flux:select.option value="{{ $make->id }}">{{ $make->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Bike model" :error="$errors->first('form.bike_model_id')">
                    <flux:select wire:model="form.bike_model_id" placeholder="Select model">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($bikeModels as $bm)
                            <flux:select.option value="{{ $bm->id }}">{{ $bm->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Reg no" :error="$errors->first('form.reg_no')">
                    <flux:input wire:model="form.reg_no" class="uppercase" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Chassis no" :error="$errors->first('form.chassis_no')">
                    <flux:input wire:model="form.chassis_no" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.color')">
                    <flux:input wire:model="form.color" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" min="1900" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Quantity" :error="$errors->first('form.quantity')">
                    <flux:input type="number" wire:model="form.quantity" min="1" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Link 1" :error="$errors->first('form.link_one')">
                    <flux:input wire:model="form.link_one" placeholder="https://…" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Link 2" :error="$errors->first('form.link_two')">
                    <flux:input wire:model="form.link_two" placeholder="https://…" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.purchase-request-items.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save item</flux:button>
        </div>
    </form>
</div>
