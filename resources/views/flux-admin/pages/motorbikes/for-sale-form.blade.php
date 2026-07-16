<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.motorbike-for-sale.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Brand New vehicles</a>
                <span>/</span>
                <span>{{ $motorcycle && $motorcycle->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motorcycle && $motorcycle->exists ? 'Edit listing' : 'New for-sale listing' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.motorbike-for-sale.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Listing details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Make" required :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" required :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input wire:model="form.year" placeholder="e.g. 2023" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Engine" :error="$errors->first('form.engine')">
                    <flux:input wire:model="form.engine" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Type" :error="$errors->first('form.type')">
                    <flux:select wire:model="form.type" placeholder="— Select —">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="Scooter">Scooter</flux:select.option>
                        <flux:select.option value="Standard">Standard</flux:select.option>
                        <flux:select.option value="Super Sport">Super Sport</flux:select.option>
                        <flux:select.option value="Touring">Touring</flux:select.option>
                        <flux:select.option value="Other">Other</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sale price (£)" :error="$errors->first('form.sale_new_price')">
                    <flux:input type="number" step="0.01" wire:model="form.sale_new_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Availability" :error="$errors->first('form.availability')">
                    <flux:select wire:model="form.availability" placeholder="— Select —">
                        <flux:select.option value="for sale">For sale</flux:select.option>
                        <flux:select.option value="sold">Sold</flux:select.option>
                        <flux:select.option value="reserved">Reserved</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" rows="4" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Listing image (website)</h2>
            <p class="mb-4 text-xs text-zinc-500 dark:text-zinc-400">Shown on the brand-new vehicles catalogue and detail page.</p>
            <x-flux-admin::field-group label="Main image" :error="$errors->first('imageUpload')">
                <input type="file" wire:model="imageUpload" accept="image/*" class="block w-full text-sm text-zinc-700 dark:text-zinc-300">
            </x-flux-admin::field-group>
            <div wire:loading wire:target="imageUpload" class="mt-1 text-xs text-zinc-500">Uploading…</div>
            @if($imageUpload)
                <img src="{{ $imageUpload->temporaryUrl() }}" alt="Preview" class="mt-3 h-40 w-auto max-w-full object-contain border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950">
            @elseif($this->currentImageUrl())
                <div class="mt-3">
                    <img src="{{ $this->currentImageUrl() }}" alt="Current listing image" class="h-40 w-auto max-w-full object-contain border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950">
                    <button type="button" wire:click="removeExistingImage" class="mt-2 text-xs font-medium text-red-600 hover:underline">Remove current image</button>
                </div>
            @endif
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.motorbike-for-sale.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
