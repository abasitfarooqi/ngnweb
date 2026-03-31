<div wire:key="finance-my-applications">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">My Finance Applications</h1>

    @if (session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($applications->isNotEmpty())
        <div class="space-y-4">
            @foreach ($applications as $app)
                @php
                    $snapshot = $app->portal_snapshot ?? [];
                @endphp
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application #{{ $app->id }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Applied: {{ \Carbon\Carbon::parse($app->created_at)->format('d M Y') }}</p>
                            
                            @if ($app->items->count() > 0)
                                @php $bike = $app->items->first()->motorbike; @endphp
                                <p class="text-sm font-medium mt-2">{{ $bike->make }} {{ $bike->model }} ({{ $bike->reg_no }})</p>
                            @endif
                        </div>
                        <div>
                            @php $status = $app->portal_status ?? ['label' => 'Pending Review', 'class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200']; @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Deposit</p>
                            <p class="text-sm font-semibold">£{{ number_format($app->deposit, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $app->is_monthly ? 'Monthly' : 'Weekly' }} Payment</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['instalment'] ?? $app->weekly_instalment), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Price</p>
                            <p class="text-sm font-semibold">£{{ number_format($app->motorbike_price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Finance Amount</p>
                            <p class="text-sm font-semibold">£{{ number_format($app->financeAmount, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Financed Principal</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['principal'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Extra Amount</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['extra'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Term (Months)</p>
                            <p class="text-sm font-semibold">{{ (int) ($snapshot['total_months'] ?? 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Months Passed</p>
                            <p class="text-sm font-semibold">{{ (int) ($snapshot['months_passed'] ?? 0) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total to Pay</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['financed_total'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Paid (Estimate)</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['total_paid'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Remaining Balance (Estimate)</p>
                            <p class="text-sm font-semibold">£{{ number_format((float) ($snapshot['remaining_balance'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Contract Files</p>
                            <p class="text-sm font-semibold">{{ (int) $app->customerContracts->count() }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Contract Date</p>
                            <p class="text-sm font-semibold">
                                {{ $app->contract_date ? \Carbon\Carbon::parse($app->contract_date)->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">First Instalment Date</p>
                            <p class="text-sm font-semibold">
                                {{ $app->first_instalment_date ? \Carbon\Carbon::parse($app->first_instalment_date)->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Contract Type</p>
                            <p class="text-sm font-semibold">{{ $app->is_subscription ? '12-Month Subscription' : ($app->is_monthly ? 'Monthly' : 'Weekly') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Vehicle Type</p>
                            <p class="text-sm font-semibold">{{ $app->is_used ? 'Used' : 'New' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Your Selection (Read Only)</p>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Logbook Transfer: {{ $app->log_book_sent ? 'Yes (contract completed successfully)' : 'No' }}</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Cancelled: {{ $app->is_cancelled ? 'Yes (contract no longer active)' : 'No' }}</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Insurance/PCN: {{ $app->insurance_pcn ? 'Yes' : 'No' }}</span>
                            @if ($app->is_subscription)
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    Subscription: {{ $snapshot['subscription_label'] ?? ('Group '.$app->subscription_option) }}
                                </span>
                            @endif
                            @if (!empty($app->sold_by))
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Sold By: {{ $app->sold_by }}</span>
                            @endif
                        </div>
                    </div>

                    @if (!empty($app->extra_items))
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Extra Items</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $app->extra_items }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No applications yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start by browsing bikes available for finance.</p>
            <div class="mt-6">
                <a href="{{ route('account.finance.browse') }}"
                    class="inline-flex items-center px-4 py-2 bg-brand-red text-white font-medium rounded hover:bg-red-700">
                    Browse Bikes
                </a>
            </div>
        </div>
    @endif
</div>
