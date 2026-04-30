<div>
    <x-flux-admin::data-table title="Vehicle database" description="DVLA compliance snapshot (road tax, MOT, insurance) for every motorbike.">
        <x-slot:actions>
            <x-flux-admin::export-button />
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
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
