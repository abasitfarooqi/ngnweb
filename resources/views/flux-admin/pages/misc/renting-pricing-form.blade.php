<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.renting-pricing.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Rental pricing</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit price' : 'New price' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.renting-pricing.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Pricing details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Weekly price (£)" required :error="$errors->first('form.weekly_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.weekly_price" placeholder="0.00" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Minimum deposit (£)" :error="$errors->first('form.minimum_deposit')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.minimum_deposit" placeholder="0.00" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike (reg / VRM)" required :error="$errors->first('form.motorbike_id')">
                    <div class="{{ count($motorbikeSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input
                            wire:model.live.debounce.300ms="motorbikeSearch"
                            placeholder="Type registration e.g. AB12 CDE"
                            autocomplete="off"
                            x-on:keydown.enter.prevent="$wire.commitMotorbikeSearch()"
                        />
                        @if(count($motorbikeSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li role="option" wire:mousedown.prevent="selectMotorbike({{ $ms['id'] }})">{{ $ms['label'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    @if(!empty($form['motorbike_id']))
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Selected ID #{{ $form['motorbike_id'] }}</p>
                    @endif
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Effective date" :error="$errors->first('form.update_date')">
                    <flux:input type="date" wire:model="form.update_date" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.iscurrent" class="accent-zinc-900 dark:accent-zinc-200"> Mark as current price
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.renting-pricing.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
