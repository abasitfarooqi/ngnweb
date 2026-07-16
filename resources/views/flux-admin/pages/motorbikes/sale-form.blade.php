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
            <flux:button type="button" wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Sale details</h2>

            <x-flux-admin::form-grid cols="3" class="mb-4">
                <x-flux-admin::field-group label="Motorbike (reg)" required span="full" :error="$errors->first('form.motorbike_id')">
                    <div class="{{ count($motorbikeSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input
                            wire:model.live.debounce.300ms="motorbikeSearch"
                            placeholder="Search by registration…"
                            autocomplete="off"
                            :disabled="$motorbikesSale && $motorbikesSale->exists"
                            x-on:keydown.enter.prevent="$wire.commitMotorbikeSearch()"
                        />
                        @if(count($motorbikeSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li role="option" wire:mousedown.prevent="selectMotorbike({{ $ms['id'] }}, @js($ms['reg']))">{{ $ms['reg'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
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
            </x-flux-admin::form-grid>

            <x-flux-admin::form-grid cols="3">
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
            </x-flux-admin::form-grid>

            <div class="mt-4 flex flex-wrap items-center gap-6">
                <flux:checkbox wire:model.live="form.is_sold" label="Sold" />
                <flux:checkbox wire:model.live="form.is_rented" label="Use for rental (hide from used sale page)" />
                <flux:checkbox wire:model.live="form.v5_available" label="V5 available" />
            </div>

            @if($form['is_rented'] ?? false)
                <p class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                    This bike stays off the public used-sale listing and counts towards Total NGN Vehicles as internal rental stock (profile 1).
                </p>
            @endif

            @if($form['is_sold'] ?? false)
                <x-flux-admin::form-grid cols="3" class="mt-4">
                    <x-flux-admin::field-group label="Buyer name" :error="$errors->first('form.buyer_name')">
                        <flux:input wire:model="form.buyer_name" />
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Buyer phone" :error="$errors->first('form.buyer_phone')">
                        <flux:input wire:model="form.buyer_phone" type="tel" />
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Buyer email" :error="$errors->first('form.buyer_email')">
                        <flux:input wire:model="form.buyer_email" type="email" />
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Buyer address" span="full" :error="$errors->first('form.buyer_address')">
                        <flux:textarea wire:model="form.buyer_address" rows="2" />
                    </x-flux-admin::field-group>
                </x-flux-admin::form-grid>
            @endif

            <x-flux-admin::form-grid cols="1" class="mt-4">
                <x-flux-admin::field-group label="Accessories" span="full" :error="$errors->first('form.accessories')">
                    <flux:editor
                        wire:model="form.accessories"
                        toolbar="bold bullet ordered | undo redo"
                        class="flux-admin-flux-editor !rounded-none"
                    />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Notes" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </x-flux-admin::form-grid>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Sale images (website)</h2>
            <p class="mb-4 text-xs text-zinc-500 dark:text-zinc-400">These appear on the used-bike listing and detail pages. Image one is the main card photo.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['field' => 'image_one', 'upload' => 'imageOneUpload', 'label' => 'Image one (main)'],
                    ['field' => 'image_two', 'upload' => 'imageTwoUpload', 'label' => 'Image two'],
                    ['field' => 'image_three', 'upload' => 'imageThreeUpload', 'label' => 'Image three'],
                    ['field' => 'image_four', 'upload' => 'imageFourUpload', 'label' => 'Image four'],
                ] as $slot)
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <x-flux-admin::field-group :label="$slot['label']" :error="$errors->first($slot['upload'])">
                            <input type="file" wire:model="{{ $slot['upload'] }}" accept="image/*" class="block w-full text-sm text-zinc-700 dark:text-zinc-300">
                        </x-flux-admin::field-group>
                        @if($this->{$slot['upload']})
                            <img src="{{ $this->{$slot['upload']}->temporaryUrl() }}" alt="Preview" class="mt-2 h-28 w-full object-contain border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950">
                        @elseif($this->currentImageUrl($form[$slot['field']] ?? null))
                            <div class="mt-2 relative">
                                <img src="{{ $this->currentImageUrl($form[$slot['field']] ?? null) }}" alt="Current" class="h-28 w-full object-contain border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950">
                                <button type="button" wire:click="removeExistingImage('{{ $slot['field'] }}')" class="mt-2 text-xs font-medium text-red-600 hover:underline">Remove current</button>
                            </div>
                        @endif
                        <div wire:loading wire:target="{{ $slot['upload'] }}" class="mt-1 text-xs text-zinc-500">Uploading…</div>
                    </div>
                @endforeach
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
