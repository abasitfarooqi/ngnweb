<div>
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recurring Payments</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Read-only view of your Judopay-linked rental and finance payment history.
            </p>
        </div>
        <div class="flex border border-gray-300 dark:border-gray-700">
            <button wire:click="setServiceFilter('all')" type="button" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider {{ $serviceFilter === 'all' ? 'bg-brand-red text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                All
            </button>
            <button wire:click="setServiceFilter('rental')" type="button" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider border-l border-gray-300 dark:border-gray-700 {{ $serviceFilter === 'rental' ? 'bg-brand-red text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                Rental
            </button>
            <button wire:click="setServiceFilter('finance')" type="button" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider border-l border-gray-300 dark:border-gray-700 {{ $serviceFilter === 'finance' ? 'bg-brand-red text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                Finance
            </button>
        </div>
    </div>

    @if ($subscriptions->isEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                No recurring payment records are currently linked to your account.
            </p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($subscriptions as $subscription)
                @php
                    $summary = $subscription->portal_payment_summary ?? [];
                    $serviceLabel = $summary['service_label'] ?? 'Service';
                    $mitSessions = $subscription->mitPaymentSessions->sortByDesc('created_at');
                @endphp
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-red">{{ $serviceLabel }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Subscription #{{ $subscription->id }}</span>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $summary['vehicle_label'] ?? 'Vehicle not available' }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Reference: {{ $subscription->consumer_reference ?: 'N/A' }} |
                                Frequency: {{ strtoupper((string) ($subscription->billing_frequency ?? 'N/A')) }} |
                                Amount: GBP {{ number_format((float) $subscription->amount, 2) }}
                            </p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 min-w-[280px]">
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Paid</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">GBP {{ number_format((float) ($summary['paid_total'] ?? 0), 2) }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Success Count</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ (int) ($summary['paid_count'] ?? 0) }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Failed Count</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ (int) ($summary['failed_count'] ?? 0) }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Queued</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ (int) ($summary['queued_count'] ?? 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-gray-600 dark:text-gray-300">
                        <span class="font-semibold">Status:</span> {{ ucfirst((string) ($subscription->status ?? 'unknown')) }}
                        <span class="mx-2 text-gray-400">|</span>
                        <span class="font-semibold">Card:</span> **** {{ $subscription->card_last_four ?: 'XXXX' }}
                        <span class="mx-2 text-gray-400">|</span>
                        <span class="font-semibold">CIT approved:</span>
                        {{ !empty($summary['cit_approved_at']) ? \Carbon\Carbon::parse($summary['cit_approved_at'])->format('d M Y H:i') : 'N/A' }}
                        <span class="mx-2 text-gray-400">|</span>
                        <span class="font-semibold">Next due:</span>
                        {{ !empty($summary['next_due_at']) ? \Carbon\Carbon::parse($summary['next_due_at'])->format('d M Y H:i') : 'Not scheduled' }}
                    </div>

                    <div class="mt-4 overflow-x-auto border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Date</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Reference</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Amount</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Status</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($mitSessions as $session)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                                            {{ optional($session->payment_completed_at ?: $session->created_at)->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $session->judopay_payment_reference ?: 'N/A' }}</td>
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">GBP {{ number_format((float) $session->amount, 2) }}</td>
                                        <td class="px-3 py-2">
                                            @php
                                                $status = strtolower((string) $session->status);
                                                $statusClass = match ($status) {
                                                    'success' => 'text-green-700 dark:text-green-300',
                                                    'declined', 'error', 'cancelled' => 'text-red-700 dark:text-red-300',
                                                    default => 'text-yellow-700 dark:text-yellow-300',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }} font-semibold uppercase tracking-wider">{{ $status ?: 'unknown' }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $session->judopay_receipt_id ?: 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-3 text-gray-500 dark:text-gray-400">No MIT payment session records.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
