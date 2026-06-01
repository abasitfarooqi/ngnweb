<div>
    <x-flux-admin::data-table title="E-bike manager" description="Electric bike fleet with registration and current rental pricing.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.ebikes.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">Add e-bike</flux:button></a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search make, model, VIN or registration…" />
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>VIN</flux:table.column>
                <flux:table.column>Colour</flux:table.column>
                <flux:table.column>Weekly price</flux:table.column>
                <flux:table.column>Deposit</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bikes as $b)
                    @php($reg = $b->registrations->first())
                    @php($price = $b->rentingPricings->first())
                    <flux:table.row wire:key="ebike-{{ $b->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $reg?->registration_number ?? $b->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->make }} {{ $b->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->year }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $b->vin_number }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $b->color }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $price ? '£'.number_format((float) $price->weekly_price, 2) : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $price ? '£'.number_format((float) $price->minimum_deposit, 2) : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.ebikes.edit', $b->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $b->id }})" wire:confirm="Delete this record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No e-bikes.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $bikes->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[720px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit e-bike' : 'Add e-bike' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Registration" :error="$errors->first('formData.reg_no')">
                    <flux:input wire:model="formData.reg_no" placeholder="e.g. AB12 CDE" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="VIN number" :error="$errors->first('formData.vin_number')">
                    <flux:input wire:model="formData.vin_number" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Make" :error="$errors->first('formData.make')">
                    <flux:input wire:model="formData.make" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Model" :error="$errors->first('formData.model')">
                    <flux:input wire:model="formData.model" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Year" :error="$errors->first('formData.year')">
                    <flux:input wire:model="formData.year" type="number" placeholder="e.g. 2023" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Colour" :error="$errors->first('formData.color')">
                    <flux:input wire:model="formData.color" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Engine" :error="$errors->first('formData.engine')">
                    <flux:input wire:model="formData.engine" placeholder="e.g. Electric" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Branch" :error="$errors->first('formData.branch_id')">
                    <flux:select wire:model="formData.branch_id" placeholder="Select branch">
                        <flux:select.option value="">— None —</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
