<div>
    <x-flux-admin::data-table
        title="MOT / TAX compliance"
        description="Direct motorbike_annual_compliance table — read-only list with row preview (not editable)."
    >
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.motorbike-compliance.index') }}" wire:navigate>
                <flux:button size="sm" variant="outline" icon="truck" class="!rounded-none">Vehicle database</flux:button>
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search REG. no, record ID or motorbike ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.road_tax_status" placeholder="Road tax">
                        <flux:select.option value="">Any road tax</flux:select.option>
                        <flux:select.option value="Taxed">Taxed</flux:select.option>
                        <flux:select.option value="TAXED">TAXED</flux:select.option>
                        <flux:select.option value="SORN">SORN</flux:select.option>
                        <flux:select.option value="UNTAXED">UNTAXED</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.mot_status" placeholder="MOT status">
                        <flux:select.option value="">Any MOT</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="Not valid">Not valid</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'motorbike_annual_compliance.id'" :direction="$sortField === 'motorbike_annual_compliance.id' ? $sortDirection : null" wire:click="sortBy('motorbike_annual_compliance.id')">ID</flux:table.column>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>REG. No</flux:table.column>
                <flux:table.column>Year</flux:table.column>
                <flux:table.column>Road tax</flux:table.column>
                <flux:table.column>Tax due</flux:table.column>
                <flux:table.column>MOT</flux:table.column>
                <flux:table.column>MOT due</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'motorbike_annual_compliance.updated_at'" :direction="$sortField === 'motorbike_annual_compliance.updated_at' ? $sortDirection : null" wire:click="sortBy('motorbike_annual_compliance.updated_at')">Updated</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="mac-preview-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-500">{{ $r->id }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->motorbike_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs font-medium text-zinc-900 dark:text-white">{{ $r->motorbike?->reg_no ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->year ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-flux-admin::status-badge :status="$r->road_tax_status" :map="[
                                'Taxed' => ['green', 'Taxed'],
                                'TAXED' => ['green', 'TAXED'],
                                'SORN' => ['yellow', 'SORN'],
                                'UNTAXED' => ['red', 'UNTAXED'],
                                'No details held by DVLA' => ['zinc', 'No details'],
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
                        <flux:table.cell class="text-xs text-zinc-500">{{ optional($r->updated_at)->format('d M Y H:i') ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" wire:click="openPreview({{ $r->id }})" class="!rounded-none">Preview</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No current compliance records.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showPreview" class="md:w-[640px]">
        @if($preview)
            <div class="space-y-4">
                <flux:heading size="lg">Compliance record #{{ $preview->id }}</flux:heading>
                <p class="text-sm text-zinc-500">Read-only — raw motorbike_annual_compliance row (current snapshot).</p>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Motorbike ID</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $preview->motorbike_id }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">REG. No</dt>
                        <dd class="font-mono font-medium text-zinc-900 dark:text-white">{{ $preview->motorbike?->reg_no ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3 sm:col-span-2">
                        <dt class="text-zinc-500 text-xs uppercase">Vehicle</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ trim(($preview->motorbike?->make ?? '').' '.($preview->motorbike?->model ?? '')) ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Compliance year</dt>
                        <dd>{{ $preview->year ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Road tax status</dt>
                        <dd>{{ $preview->road_tax_status ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Tax due date</dt>
                        <dd>{{ $preview->tax_due_date ? \Carbon\Carbon::parse($preview->tax_due_date)->format('d M Y') : '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">MOT status</dt>
                        <dd>{{ $preview->mot_status ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">MOT due date</dt>
                        <dd>{{ $preview->mot_due_date ? \Carbon\Carbon::parse($preview->mot_due_date)->format('d M Y') : '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Insurance status</dt>
                        <dd>{{ $preview->insurance_status ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Insurance due date</dt>
                        <dd>{{ $preview->insurance_due_date ? \Carbon\Carbon::parse($preview->insurance_due_date)->format('d M Y') : '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Created</dt>
                        <dd>{{ optional($preview->created_at)->format('d M Y H:i:s') ?: '—' }}</dd>
                    </div>
                    <div class="border border-zinc-200 dark:border-zinc-700 p-3">
                        <dt class="text-zinc-500 text-xs uppercase">Updated</dt>
                        <dd>{{ optional($preview->updated_at)->format('d M Y H:i:s') ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="flex justify-end pt-2">
                    <flux:button variant="ghost" wire:click="closePreview" class="!rounded-none">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
