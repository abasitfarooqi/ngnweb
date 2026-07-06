<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-sales.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Motorbike sales</a>
                <span>/</span>
                <span>{{ $motorbikesSale && $motorbikesSale->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motorbikesSale && $motorbikesSale->exists ? 'Edit sale' : 'New sale' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-sales.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Sale details</h2>

            {{-- Motorbike search --}}
            <div class="mb-4">
                <x-flux-admin::field-group label="Motorbike (reg)" required :error="$errors->first('form.motorbike_id')">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="motorbikeSearch" placeholder="Search by registration…" autocomplete="off" />
                        @if(count($motorbikeSuggestions))
                            <ul class="absolute z-50 mt-0.5 w-full border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-44 overflow-y-auto">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li wire:click="selectMotorbike({{ $ms['id'] }}, '{{ addslashes($ms['reg']) }}')"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ $ms['reg'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Condition" :error="$errors->first('form.condition')">
                    <flux:input wire:model="form.condition" placeholder="e.g. Good, Fair" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Mileage" :error="$errors->first('form.mileage')">
                    <flux:input wire:model="form.mileage" type="number" step="0.01" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Price (£)" :error="$errors->first('form.price')">
                    <flux:input wire:model="form.price" type="number" step="0.01" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Purchase date" :error="$errors->first('form.date_of_purchase')">
                    <flux:input wire:model="form.date_of_purchase" type="date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Sale date" :error="$errors->first('form.date_of_sale')">
                    <flux:input wire:model="form.date_of_sale" type="date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Engine" :error="$errors->first('form.engine')">
                    <flux:input wire:model="form.engine" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Suspension" :error="$errors->first('form.suspension')">
                    <flux:input wire:model="form.suspension" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Brakes" :error="$errors->first('form.brakes')">
                    <flux:input wire:model="form.brakes" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Belt" :error="$errors->first('form.belt')">
                    <flux:input wire:model="form.belt" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Electrical" :error="$errors->first('form.electrical')">
                    <flux:input wire:model="form.electrical" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Tires" :error="$errors->first('form.tires')">
                    <flux:input wire:model="form.tires" />
                </x-flux-admin::field-group>
            </div>

            @if($form['is_sold'] ?? false)
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <x-flux-admin::field-group label="Buyer name" :error="$errors->first('form.buyer_name')">
                        <flux:input wire:model="form.buyer_name" />
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Buyer phone" :error="$errors->first('form.buyer_phone')">
                        <flux:input wire:model="form.buyer_phone" type="tel" />
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Buyer email" :error="$errors->first('form.buyer_email')">
                        <flux:input wire:model="form.buyer_email" type="email" />
                    </x-flux-admin::field-group>
                </div>
                <div class="mt-4">
                    <x-flux-admin::field-group label="Buyer address" :error="$errors->first('form.buyer_address')">
                        <flux:textarea wire:model="form.buyer_address" rows="2" />
                    </x-flux-admin::field-group>
                </div>
            @endif

            <div class="mt-4">
                <x-flux-admin::field-group label="Accessories" :error="$errors->first('form.accessories')">
                    <flux:textarea wire:model="form.accessories" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.live="form.is_sold" class="accent-zinc-900 dark:accent-zinc-200"> Sold
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.v5_available" class="accent-zinc-900 dark:accent-zinc-200"> V5 available
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-sales.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
