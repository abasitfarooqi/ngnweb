<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">My Rentals</h1>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No rentals yet</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Get started by browsing our available motorbikes.</p>
            <div class="mt-6">
                <a href="{{ route('account.rentals.browse') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                    Browse Motorbikes
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                @php
                    $activeItem = $booking->rentingBookingItems->whereNull('end_date')->first();
                    $isEnded = $booking->rentingBookingItems->isNotEmpty() && $booking->rentingBookingItems->every(fn ($i) => !empty($i->end_date));
                    $displayState = $isEnded ? 'ENDED' : (string) ($booking->state ?? 'ACTIVE');
                    $isActive = !$isEnded && (bool) $activeItem;
                    $statusClass = match($displayState) {
                        'ACTIVE' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                        'PENDING_RETURN' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                        'COMPLETED', 'ENDED' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                        'CANCELLED' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                        default => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                    };
                @endphp

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Booking #{{ $booking->id }}
                                </h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $displayState }}
                                </span>
                            </div>
                            @if ($activeItem && $activeItem->motorbike)
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    <strong class="text-gray-900 dark:text-white">{{ $activeItem->motorbike->make }} {{ $activeItem->motorbike->model }}</strong>
                                    ({{ $activeItem->motorbike->reg_no }})
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Start: {{ \Carbon\Carbon::parse($activeItem->start_date)->format('d/m/Y') }}
                                    @if ($activeItem->due_date)
                                        | Due: {{ \Carbon\Carbon::parse($activeItem->due_date)->format('d/m/Y') }}
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Weekly Rent: £{{ number_format($activeItem->weekly_rent, 2) }}
                                </p>
                            @endif
                            @php
                                $historicalItems = $booking->rentingBookingItems->whereNotNull('end_date');
                            @endphp
                            @if($historicalItems->isNotEmpty())
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Previous bikes in this booking:
                                    @foreach($historicalItems as $item)
                                        @if($item->motorbike)
                                            <span class="inline-block mr-2">{{ $item->motorbike->reg_no }} ({{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : 'N/A' }} - {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') : 'N/A' }})</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center flex-wrap gap-2">
                            <button type="button" wire:click="showPayments({{ $booking->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Payment History
                            </button>
                            @if ($isActive)
                                <button type="button" wire:click="openExtendModal({{ $booking->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Extend
                                </button>
                                <button type="button" wire:click="openReturnModal({{ $booking->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Submit Return Notice
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($booking->bookingInvoices->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Invoices</h4>
                            <div class="space-y-1">
                                @foreach ($booking->bookingInvoices as $invoice)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            Invoice #{{ $invoice->id }} - £{{ number_format($invoice->amount, 2) }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $invoice->status }})</span>
                                        </span>
                                        <button type="button" wire:click="downloadInvoice({{ $invoice->id }})"
                                            class="text-brand-red hover:text-red-700 font-medium">
                                            Download PDF
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($showPaymentHistory && $selectedBooking)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="payments-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment History – Booking #{{ $selectedBooking }}</h3>
                    <button type="button" wire:click="closePayments" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if ($paymentHistory->isEmpty())
                    <p class="text-sm text-gray-600 dark:text-gray-400">No payment history available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($paymentHistory as $transaction)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->transactionType?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->paymentMethod?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">£{{ number_format($transaction->amount, 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->bookingInvoice ? 'Invoice #' . $transaction->invoice_id : '–' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4 flex justify-end">
                    <button type="button" wire:click="closePayments"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showExtendModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="extend-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Extend Rental Period</h3>
                <div class="mb-4">
                    <label for="extendWeeks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of weeks to extend</label>
                    <input type="number" wire:model="extendWeeks" id="extendWeeks" min="1" max="52"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    @error('extendWeeks') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeExtendModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" wire:click="extendRental"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                        Confirm Extension
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showReturnModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="return-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Submit Return Notice</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Please provide your intended return date and any additional notes.</p>
                <div class="mb-4">
                    <label for="returnNotice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Details</label>
                    <textarea wire:model="returnNotice" id="returnNotice" rows="4" placeholder="e.g., I would like to return the motorbike on 25/03/2026. Please advise on the return process."
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"></textarea>
                    @error('returnNotice') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeReturnModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitReturnNotice"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                        Submit Notice
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
