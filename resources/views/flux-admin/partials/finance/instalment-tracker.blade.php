<div>
    @if($subscription)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Status</p>
                <p class="text-sm mt-0.5">
                    @if($subscription->status === 'active')
                        <flux:badge color="green" size="sm">Active</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">{{ ucfirst($subscription->status ?? 'Unknown') }}</flux:badge>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Billing Frequency</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ ucfirst($subscription->billing_frequency ?? '—') }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Amount</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">£{{ number_format($subscription->amount ?? 0, 2) }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Opening Balance</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">£{{ number_format($subscription->opening_balance ?? 0, 2) }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Billing Day</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $subscription->billing_day ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Start Date</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $subscription->start_date?->format('d M Y') ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">End Date</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $subscription->end_date?->format('d M Y') ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Card</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                    @if($subscription->card_last_four)
                        •••• {{ $subscription->card_last_four }}
                        @if($subscription->card_funding)
                            <span class="text-zinc-500 dark:text-zinc-400 text-xs ml-1">({{ ucfirst($subscription->card_funding) }})</span>
                        @endif
                    @else
                        —
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Issuing Bank</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $subscription->issuing_bank ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Consumer Reference</p>
                <p class="text-sm text-zinc-900 dark:text-white mt-0.5 font-mono text-xs">{{ $subscription->consumer_reference ?? '—' }}</p>
            </div>
        </div>
    @else
        <div class="py-8 text-center">
            <flux:icon name="credit-card" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No JudoPay subscription linked to this application.</p>
        </div>
    @endif
</div>
