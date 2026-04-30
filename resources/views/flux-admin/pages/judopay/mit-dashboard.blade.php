<div class="space-y-6">
    <x-flux-admin::summary-header title="MIT dashboard" subtitle="Recurring Judopay merchant-initiated transactions across active subscriptions.">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" icon="calendar-days" :href="route('flux-admin.judopay-weekly-queue.index')" class="!rounded-none">Weekly schedule</flux:button>
            <flux:button size="sm" variant="ghost" icon="credit-card" :href="route('flux-admin.judopay-recurring.index')" class="!rounded-none">Onboarded customers</flux:button>
            <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="url(config('backpack.base.route_prefix').'/judopay/mit-dashboard')" class="!rounded-none">Legacy full view</flux:button>
        </x-slot:actions>
    </x-flux-admin::summary-header>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <x-flux-admin::stat-card label="Active subs" :value="$stats['active_subs']" icon="credit-card" colour="indigo" />
        <x-flux-admin::stat-card label="Total runs" :value="$stats['sessions_total']" icon="arrows-right-left" />
        <x-flux-admin::stat-card label="Success" :value="$stats['sessions_success']" icon="check-circle" colour="green" />
        <x-flux-admin::stat-card label="Failed" :value="$stats['sessions_failed']" icon="x-circle" colour="red" />
        <x-flux-admin::stat-card label="Pending" :value="$stats['sessions_pending']" icon="clock" colour="amber" />
    </div>

    <x-flux-admin::data-table title="Recent MIT payment sessions" description="Most recent recurring runs across all active subscriptions.">
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="status" placeholder="Status">
                        <flux:select.option value="">Any status</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="processing">Processing</flux:select.option>
                        <flux:select.option value="success">Success</flux:select.option>
                        <flux:select.option value="failed">Failed</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>When</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Subscription</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Receipt</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    @php
                        $cust = $r->subscription?->judopayOnboarding?->onboardable;
                        $name = $cust?->full_name ?? $cust?->name ?? ($cust?->first_name.' '.$cust?->last_name);
                    @endphp
                    <flux:table.row wire:key="mit-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->created_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell>{{ trim((string) $name) ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $r->subscription?->consumer_reference ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">£{{ number_format((float) $r->amount, 2) }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $r->judopay_receipt_id ?: '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No MIT sessions yet.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
