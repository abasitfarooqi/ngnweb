<div>
    <x-flux-admin::data-table title="Vehicle database" description="DVLA compliance snapshot (road tax, MOT, insurance) for every motorbike.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.motorbike-compliance.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New record</flux:button></a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search registration, make or model…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.road_tax_status" placeholder="Road tax">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="TAXED">TAXED</flux:select.option>
                        <flux:select.option value="SORN">SORN</flux:select.option>
                        <flux:select.option value="UNTAXED">UNTAXED</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.mot_status" placeholder="MOT status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Not valid">Not valid</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Reg</flux:table.column>
                <flux:table.column>Make / Model</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>Road tax</flux:table.column>
                <flux:table.column>Tax due</flux:table.column>
                <flux:table.column>MOT</flux:table.column>
                <flux:table.column>MOT due</flux:table.column>
                <flux:table.column>Association</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="comp-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->motorbike?->make }} {{ $r->motorbike?->model }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->motorbike?->year }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->motorbike?->engine }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="$r->road_tax_status" :map="[
                                'TAXED' => ['green', 'TAXED'],
                                'SORN' => ['yellow', 'SORN'],
                                'UNTAXED' => ['red', 'UNTAXED'],
                            ]" />
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->tax_due_date ? \Carbon\Carbon::parse($r->tax_due_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="$r->mot_status" :map="[
                                'Valid' => ['green', 'Valid'],
                                'Not valid' => ['red', 'Not valid'],
                                'No details held by DVLA' => ['zinc', 'No details'],
                            ]" />
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-700 dark:text-zinc-300">{{ $r->association_status ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.motorbike-compliance.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this compliance record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[700px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit compliance record' : 'New compliance record' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Motorbike ID" required :error="$errors->first('formData.motorbike_id')">
                    <flux:input type="number" wire:model="formData.motorbike_id" placeholder="Motorbike ID" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" required :error="$errors->first('formData.year')">
                    <flux:input wire:model="formData.year" placeholder="e.g. 2024" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="MOT status" :error="$errors->first('formData.mot_status')">
                    <flux:select wire:model="formData.mot_status">
                        <flux:select.option value="">— select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="MOT due date" :error="$errors->first('formData.mot_due_date')">
                    <flux:input type="date" wire:model="formData.mot_due_date" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Road tax status" :error="$errors->first('formData.road_tax_status')">
                    <flux:select wire:model="formData.road_tax_status">
                        <flux:select.option value="">— select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Tax due date" :error="$errors->first('formData.tax_due_date')">
                    <flux:input type="date" wire:model="formData.tax_due_date" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Insurance status" :error="$errors->first('formData.insurance_status')">
                    <flux:select wire:model="formData.insurance_status">
                        <flux:select.option value="">— select —</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Invalid">Invalid</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                        <flux:select.option value="Unknown">Unknown</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Insurance due date" :error="$errors->first('formData.insurance_due_date')">
                    <flux:input type="date" wire:model="formData.insurance_due_date" />
                </x-flux-admin::field-group>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
