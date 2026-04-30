<div>
    <x-flux-admin::data-table title="PCN case updates" description="Progression log for penalty charge notices including appeals and payments.">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/pcn-case-update/create')" class="!rounded-none">New update</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search PCN number or registration…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_appealed" placeholder="Appealed">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Yes</flux:select.option>
                        <flux:select.option value="0">No</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.paid_status" placeholder="Status">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="owner">Paid by NGN</flux:select.option>
                        <flux:select.option value="keeper">Paid by keeper</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>PCN</flux:table.column>
                <flux:table.column>VRN</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'update_date'" :direction="$sortField === 'update_date' ? $sortDirection : null" wire:click="sortBy('update_date')">Date</flux:table.column>
                <flux:table.column>Appealed</flux:table.column>
                <flux:table.column>NGN paid</flux:table.column>
                <flux:table.column>Keeper paid</flux:table.column>
                <flux:table.column>Cancelled</flux:table.column>
                <flux:table.column>Fee</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="pcn-u-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->pcncase?->pcn_number }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->pcncase?->motorbike?->reg_no }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->update_date ? \Carbon\Carbon::parse($r->update_date)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_appealed" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_paid_by_owner" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_paid_by_keeper" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $r->is_cancled" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">£{{ number_format((float) $r->additional_fee, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/pcn-tol-request/create?update_id='.$r->id)" icon="document-text" class="!rounded-none">TOL</flux:button>
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/pcn-case-update/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No updates.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
