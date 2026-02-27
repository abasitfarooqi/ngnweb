@php
    $bookingId = request()->route('bookingId');
    $booking = \App\Models\RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])->findOrFail($bookingId);
    $invoice = \App\Models\BookingInvoice::where('booking_id', $bookingId)->where('is_paid', false)->first();
@endphp

<x-layouts.portal>
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Complete Your Payment</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Booking #{{ $booking->id }}</p>
        </div>

        {{-- Booking Summary --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Booking Summary</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Start Date</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $booking->start_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Rental Period</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $booking->start_date->diffInWeeks($booking->due_date) }} Week(s)</span>
                </div>
                @if ($booking->rentingBookingItems->first())
                    @php $item = $booking->rentingBookingItems->first(); @endphp
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Motorbike</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $item->motorbike->make ?? '' }} {{ $item->motorbike->model ?? '' }} ({{ $item->motorbike->reg_no ?? '' }})
                        </span>
                    </div>
                @endif
            </div>
        </div>

        @if ($invoice)
            {{-- Payment Options --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Details</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Security Deposit</span>
                        <span class="font-medium text-gray-900 dark:text-white">£{{ number_format($invoice->deposit ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">First Week Rent</span>
                        <span class="font-medium text-gray-900 dark:text-white">£{{ number_format(($invoice->amount - $invoice->deposit), 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-semibold text-gray-900 dark:text-white">Total Due Now</span>
                            <span class="text-2xl font-bold text-red-600">£{{ number_format($invoice->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded mb-6">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Important:</strong> This payment will set up automatic weekly payments of £{{ number_format(($invoice->amount - $invoice->deposit), 2) }} 
                        via Judopay recurring payments. Your deposit will be refunded when you return the motorbike in good condition.
                    </p>
                </div>

                {{-- Payment Method Selection --}}
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-900 dark:text-white">Select Payment Method</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        {{-- Judopay Card Payment (Recurring Setup) --}}
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:border-red-600 transition cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Card Payment (Recurring)</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Secure payment via Judopay</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Recommended</span>
                            </div>
                            <div class="mt-3">
                                <form method="POST" action="{{ route('account.rentals.payment.initialize', $booking->id) }}">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                        Pay with Card
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- In-Store Payment --}}
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Pay at Branch</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Visit our branch to complete payment</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('account.rentals') }}" 
                                    class="w-full block text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    I'll Pay at Branch
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <p class="text-sm text-red-800 dark:text-red-200">
                    No pending invoice found for this booking. Please contact support.
                </p>
            </div>
        @endif

        {{-- Back Button --}}
        <div>
            <a href="{{ route('account.rentals') }}" 
                class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Rentals
            </a>
        </div>
    </div>
</x-layouts.portal>
