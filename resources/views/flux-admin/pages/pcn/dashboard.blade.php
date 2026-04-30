<div class="space-y-6">
    <div class="px-4 sm:px-6 lg:px-8 pt-4">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">PCN statistics</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Operational overview of penalty charge notices across the fleet.</p>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach([
            ['Total', $totalCases, 'text-zinc-900 dark:text-white'],
            ['Open', $openCases, 'text-amber-600 dark:text-amber-400'],
            ['Closed', $closedCases, 'text-emerald-600 dark:text-emerald-400'],
            ['Cancelled', $cancelledCases, 'text-blue-600 dark:text-blue-400'],
            ['Appealed', $appealedCases, 'text-purple-600 dark:text-purple-400'],
        ] as [$label, $value, $colour])
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ $label }}</div>
                <div class="mt-1 text-2xl font-semibold {{ $colour }}">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    <div class="px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
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

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">Top offending vehicles (open PCNs)</div>
                </div>
            </div>
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

    <div class="px-4 sm:px-6 lg:px-8 pb-8">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white">Open PCN list with WhatsApp reminder</div>
            </div>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>PCN</flux:table.column>
                    <flux:table.column>Customer</flux:table.column>
                    <flux:table.column>VRN</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
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
                        <flux:table.row><flux:table.cell colspan="6" class="text-center py-4 text-zinc-500 dark:text-zinc-400">No open PCNs.</flux:table.cell></flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
