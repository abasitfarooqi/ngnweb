<div class="space-y-6">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">My MOT Bookings</flux:heading>
        <flux:button href="{{ route('account.mot.book') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            Book MOT
        </flux:button>
    </div>

    @if($bookings->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="calendar" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400 mb-4">You haven't booked any MOT appointments yet.</p>
            <flux:button href="{{ route('account.mot.book') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                Book Your First MOT
            </flux:button>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <flux:card class="p-6">
                    <div class="flex justify-between items-start gap-4 flex-wrap">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $booking->vehicle_registration }}</h3>
                                <flux:badge
                                    color="{{ $booking->status === 'confirmed' ? 'green' : ($booking->status === 'pending' ? 'yellow' : ($booking->status === 'completed' ? 'blue' : ($booking->status === 'cancelled' ? 'red' : 'zinc'))) }}">
                                    {{ ucfirst($booking->status) }}
                                </flux:badge>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">Customer: <strong class="text-gray-900 dark:text-white">{{ $booking->customer_name }}</strong></p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Date: <strong class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->date_of_appointment)->format('d M Y') }}</strong>
                                    at <strong class="text-gray-900 dark:text-white">{{ $booking->time_slot }}</strong>
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">Branch: <strong class="text-gray-900 dark:text-white">{{ $booking->branch->name ?? 'N/A' }}</strong></p>
                            </div>
                            @if($booking->notes)
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Notes:</strong> {{ $booking->notes }}</p>
                                </div>
                            @endif
                            @if($booking->test_result)
                                <flux:callout
                                    variant="{{ $booking->test_result === 'pass' ? 'success' : 'danger' }}"
                                    icon="{{ $booking->test_result === 'pass' ? 'check-circle' : 'x-circle' }}"
                                    class="mt-3">
                                    <flux:callout.text>MOT Result: {{ strtoupper($booking->test_result) }}</flux:callout.text>
                                </flux:callout>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 flex-shrink-0">Booked: {{ $booking->created_at->format('d M Y') }}</p>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <div class="mt-4">{{ $bookings->links() }}</div>
    @endif
</div>
