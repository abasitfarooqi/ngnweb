<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Customer</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                @if($application->customer)
                    {{ $application->customer->first_name }} {{ $application->customer->last_name }}
                @else
                    <span class="text-zinc-400">—</span>
                @endif
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Created By</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                {{ $application->user?->first_name ?? '—' }} {{ $application->user?->last_name ?? '' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Sold By</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                {{ $soldBy ? ($soldBy->first_name . ' ' . $soldBy->last_name) : '—' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Contract Date</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                {{ $application->contract_date ? \Carbon\Carbon::parse($application->contract_date)->format('d M Y') : '—' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">First Instalment Date</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                {{ $application->first_instalment_date ? \Carbon\Carbon::parse($application->first_instalment_date)->format('d M Y') : '—' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Weekly Instalment</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">£{{ number_format($application->weekly_instalment ?? 0, 2) }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Deposit</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">£{{ number_format($application->deposit ?? 0, 2) }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Motorbike Price</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">£{{ number_format($application->motorbike_price ?? 0, 2) }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Contract Type</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                <flux:badge color="zinc" size="sm">{{ $contractType }}</flux:badge>
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Is Monthly</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $application->is_monthly ? 'Yes' : 'No' }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Log Book Sent</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $application->log_book_sent ? 'Yes' : 'No' }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Logbook Transfer Date</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                {{ $application->logbook_transfer_date ? \Carbon\Carbon::parse($application->logbook_transfer_date)->format('d M Y') : '—' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Subscription Option</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $application->subscription_option ?? '—' }}</p>
        </div>

        <div>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Extra Items (legacy)</p>
            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">{{ $application->extra_items ?? '—' }}</p>
        </div>
    </div>

    @if($application->notes)
        <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Notes</p>
            <p class="text-sm text-zinc-900 dark:text-white whitespace-pre-line">{{ $application->notes }}</p>
        </div>
    @endif

    @if($application->is_cancelled)
        <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Cancellation</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Cancelled At</p>
                    <p class="text-sm text-zinc-900 dark:text-white">{{ $application->cancelled_at ? \Carbon\Carbon::parse($application->cancelled_at)->format('d M Y H:i') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Reason</p>
                    <p class="text-sm text-zinc-900 dark:text-white">{{ $application->reason_of_cancellation ?? '—' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
