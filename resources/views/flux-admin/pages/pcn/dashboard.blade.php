<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            @if(empty($embedded))
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">PCN statistics</h1>
            @else
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">PCN statistics</h2>
            @endif
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Operational overview of penalty charge notices across the fleet.</p>
        </div>
        <a href="{{ route('flux-admin.pcn.index') }}" wire:navigate>
            <flux:button size="sm" variant="ghost" class="!rounded-none">Open PCN cases</flux:button>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <a href="{{ route('flux-admin.pcn.index') }}" wire:navigate class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-zinc-400 dark:hover:border-zinc-600 transition">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Total</div>
            <div class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($totalCases) }}</div>
        </a>
        <a href="{{ route('flux-admin.pcn.index', ['isClosed' => 0]) }}" wire:navigate class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-zinc-400 dark:hover:border-zinc-600 transition">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Open</div>
            <div class="mt-1 text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ number_format($openCases) }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Outstanding £{{ number_format((float) $totalFullAmount, 2) }}</div>
        </a>
        <a href="{{ route('flux-admin.pcn.index', ['isClosed' => 0, 'has_been_appealed' => 1]) }}" wire:navigate class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-zinc-400 dark:hover:border-zinc-600 transition">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Appealed (open)</div>
            <div class="mt-1 text-2xl font-semibold text-purple-600 dark:text-purple-400">{{ number_format($appealedCases) }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Police {{ $appealedStats['police'] }} · Regular {{ $appealedStats['regular'] }}</div>
        </a>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Closed</div>
            <div class="mt-1 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($closedCases) }}</div>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Cancelled</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ number_format($cancelledCases) }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Police {{ $cancelledStats['police'] }} · Regular {{ $cancelledStats['regular'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Outstanding amounts (open cases)</div>
            <dl class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Full amount</dt><dd class="font-semibold text-zinc-900 dark:text-white">£{{ number_format((float) $totalFullAmount, 2) }}</dd></div>
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Reduced amount</dt><dd class="font-semibold text-zinc-900 dark:text-white">£{{ number_format((float) $totalReducedAmount, 2) }}</dd></div>
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Police outstanding</dt><dd class="font-semibold text-zinc-900 dark:text-white">£{{ number_format((float) $outstandingAmounts['police'], 2) }}</dd></div>
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Regular outstanding</dt><dd class="font-semibold text-zinc-900 dark:text-white">£{{ number_format((float) $outstandingAmounts['regular'], 2) }}</dd></div>
            </dl>
        </div>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Case source</div>
            <dl class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Police PCNs</dt><dd class="font-semibold text-zinc-900 dark:text-white">{{ number_format($policeStats['police']) }}</dd></div>
                <div class="py-2 flex justify-between"><dt class="text-zinc-500 dark:text-zinc-400">Regular PCNs</dt><dd class="font-semibold text-zinc-900 dark:text-white">{{ number_format($policeStats['regular']) }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="space-y-4"
         x-data="pcnCharts(@js([
             'months' => $monthlyStats->pluck('month'),
             'total' => $monthlyStats->pluck('total'),
             'open' => $monthlyStats->pluck('open'),
             'closed' => $monthlyStats->pluck('closed'),
             'status' => [$openCases, $closedCases, $cancelledCases, $appealedCases],
             'police' => [$policeStats['police'], $policeStats['regular']],
             'amounts' => [(float) $outstandingAmounts['police'], (float) $outstandingAmounts['regular']],
         ]))"
         x-init="init()">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 lg:col-span-2">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Monthly trend (12 months)</div>
                <div class="relative h-56 w-full max-h-56">
                    <canvas x-ref="monthly"></canvas>
                </div>
            </div>
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Status mix</div>
                <div class="relative h-56 w-full max-h-56">
                    <canvas x-ref="status"></canvas>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Police vs regular</div>
                <div class="relative h-52 w-full max-h-52">
                    <canvas x-ref="police"></canvas>
                </div>
            </div>
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Outstanding by type</div>
                <div class="relative h-52 w-full max-h-52">
                    <canvas x-ref="amounts"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white">Top offending vehicles (open PCNs)</div>
            </div>
            <div class="touch-pan-x overflow-x-auto">
                <div class="min-w-[32rem] md:min-w-0">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>VRN</flux:table.column>
                            <flux:table.column>Customer</flux:table.column>
                            <flux:table.column>Open PCNs</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($topVehicles as $v)
                                <flux:table.row wire:key="veh-{{ $v->motorbike_id }}-{{ $v->customer_id }}">
                                    <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $v->motorbike?->reg_no ?? '—' }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $v->customer ? $v->customer->first_name.' '.$v->customer->last_name : '—' }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-900 dark:text-white">{{ $v->pcn_count }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row><flux:table.cell colspan="3" class="text-center py-4 text-zinc-500 dark:text-zinc-400">No data.</flux:table.cell></flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        </div>
    </div>

    <div class="pb-8">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white">Open PCN list with WhatsApp reminder</div>
                <flux:select wire:model.live="listSort" class="w-44">
                    <flux:select.option value="desc">Newest created</flux:select.option>
                    <flux:select.option value="asc">Oldest created</flux:select.option>
                </flux:select>
            </div>
            <div class="touch-pan-x overflow-x-auto">
                <div class="min-w-[48rem] md:min-w-0">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>PCN</flux:table.column>
                            <flux:table.column>Customer</flux:table.column>
                            <flux:table.column>VRN</flux:table.column>
                            <flux:table.column>Amount</flux:table.column>
                            <flux:table.column>WhatsApp sent</flux:table.column>
                            <flux:table.column>Last reminder</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($pcnList as $p)
                                <flux:table.row wire:key="pcn-row-{{ $p->id }}">
                                    <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $p->pcn_number }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-900 dark:text-white">{{ $p->customer_name }}</flux:table.cell>
                                    <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $p->reg_no }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-900 dark:text-white">£{{ number_format((float) $p->amount, 2) }}</flux:table.cell>
                                    <flux:table.cell>{{ $p->is_whatsapp_sent ? 'Yes' : 'No' }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-600 dark:text-zinc-400 text-xs">{{ $p->whatsapp_last_reminder_sent_at }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex gap-1">
                                            @if($p->whatsapp_url !== '#')
                                                <flux:button size="xs" variant="ghost" :href="$p->whatsapp_url" target="_blank" icon="chat-bubble-left-right" class="!rounded-none">WhatsApp</flux:button>
                                            @endif
                                            <flux:button size="xs" variant="ghost" wire:click="sendReminder({{ $p->id }})" icon="bell" class="!rounded-none">Mark sent</flux:button>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row><flux:table.cell colspan="7" class="text-center py-4 text-zinc-500 dark:text-zinc-400">No open PCNs.</flux:table.cell></flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endassets

@script
<script>
Alpine.data('pcnCharts', (payload) => ({
    payload,
    charts: [],
    chartOptions(extra = {}) {
        return Object.assign({
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        }, extra);
    },
    init() {
        if (typeof Chart === 'undefined') return;
        this.charts.push(new Chart(this.$refs.monthly, {
            type: 'line',
            data: {
                labels: this.payload.months,
                datasets: [
                    { label: 'Total', data: this.payload.total, borderColor: '#18181b', tension: 0.25 },
                    { label: 'Open', data: this.payload.open, borderColor: '#d97706', tension: 0.25 },
                    { label: 'Closed', data: this.payload.closed, borderColor: '#059669', tension: 0.25 },
                ],
            },
            options: this.chartOptions(),
        }));
        this.charts.push(new Chart(this.$refs.status, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Closed', 'Cancelled', 'Appealed'],
                datasets: [{ data: this.payload.status, backgroundColor: ['#d97706', '#059669', '#2563eb', '#9333ea'] }],
            },
            options: this.chartOptions(),
        }));
        this.charts.push(new Chart(this.$refs.police, {
            type: 'pie',
            data: {
                labels: ['Police', 'Regular'],
                datasets: [{ data: this.payload.police, backgroundColor: ['#dc2626', '#52525b'] }],
            },
            options: this.chartOptions(),
        }));
        this.charts.push(new Chart(this.$refs.amounts, {
            type: 'bar',
            data: {
                labels: ['Police', 'Regular'],
                datasets: [{ label: 'Outstanding £', data: this.payload.amounts, backgroundColor: ['#dc2626', '#52525b'] }],
            },
            options: this.chartOptions({ plugins: { legend: { display: false } } }),
        }));
    },
}));
</script>
@endscript
