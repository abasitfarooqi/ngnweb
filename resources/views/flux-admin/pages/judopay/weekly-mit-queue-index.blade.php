<div class="space-y-6">
    <x-flux-admin::summary-header
        title="Weekly MIT schedule"
        :subtitle="'Week of '.$weekStart->format('d M Y').' – '.$weekEnd->format('d M Y')">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" icon="chevron-left" wire:click="goTo('prev')" class="!rounded-none">Previous</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="goToday" class="!rounded-none">Today</flux:button>
            <flux:button size="sm" variant="ghost" icon="chevron-right" wire:click="goTo('next')" class="!rounded-none">Next</flux:button>
            <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="url(config('backpack.base.route_prefix').'/judopay/weekly-mit-queue?week='.$weekStart->toDateString())" class="!rounded-none">Legacy actions</flux:button>
        </x-slot:actions>
    </x-flux-admin::summary-header>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-flux-admin::stat-card label="Scheduled" :value="$summary['total']" icon="queue-list" />
        <x-flux-admin::stat-card label="Cleared" :value="$summary['cleared']" icon="check-circle" colour="green" />
        <x-flux-admin::stat-card label="Declined" :value="$summary['declined']" icon="x-circle" colour="red" />
        <x-flux-admin::stat-card label="Pending" :value="$summary['pending']" icon="clock" colour="amber" />
    </div>

    <x-flux-admin::data-table title="Queue items" description="Every NGN MIT queue entry whose invoice date falls inside the selected week.">
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="sortMode" placeholder="Sort">
                        <flux:select.option value="">By invoice date</flux:select.option>
                        <flux:select.option value="success">Successes first</flux:select.option>
                        <flux:select.option value="decline">Declines first</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Invoice date</flux:table.column>
                <flux:table.column>Fire date</flux:table.column>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Attempt</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Cleared</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($items as $item)
                    @php
                        $cust = $item->subscribable?->judopayOnboarding?->onboardable;
                        $name = $cust?->full_name ?? $cust?->name ?? trim(($cust?->first_name ?? '').' '.($cust?->last_name ?? ''));
                    @endphp
                    <flux:table.row wire:key="wmq-{{ $item->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $item->invoice_date ? \Carbon\Carbon::parse($item->invoice_date)->format('D d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $item->mit_fire_date ? \Carbon\Carbon::parse($item->mit_fire_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $item->invoice_number }}</flux:table.cell>
                        <flux:table.cell>{{ trim((string) $name) ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $item->mit_attempt }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$item->status" /></flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="(bool) $item->cleared" /></flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">Nothing scheduled for this week.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-flux-admin::data-table>
</div>
