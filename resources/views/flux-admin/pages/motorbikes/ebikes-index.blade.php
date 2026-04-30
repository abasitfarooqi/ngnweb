<div>
    <x-flux-admin::data-table title="E-bike manager" description="Electric bike fleet with registration and current rental pricing.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="route('page.ebike_manager.index')" class="!rounded-none">Add E-bike</flux:button>
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
                            <flux:button size="xs" variant="ghost" :href="route('page.ebike_manager.index').'#ebike-'.$b->id" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No e-bikes.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $bikes->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
