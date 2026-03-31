<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-3">
        <flux:heading size="xl">Bookings</flux:heading>
        <div class="flex gap-3">
            <flux:button href="{{ route('account.mot.book') }}" variant="outline">Book MOT</flux:button>
            <flux:button href="{{ route('account.repairs.request') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Repairs</flux:button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-6">
            @foreach(['all' => 'All Bookings', 'upcoming' => 'Upcoming', 'completed' => 'Completed'] as $tab => $label)
                <button type="button" wire:click="switchTab('{{ $tab }}')"
                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition {{ $activeTab === $tab ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    @if($bookings->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="calendar" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400 mb-4">You haven't made any bookings yet.</p>
            <div class="flex gap-3 justify-center flex-wrap">
                <flux:button href="{{ route('account.mot.book') }}" variant="outline">Book MOT</flux:button>
                <flux:button href="{{ route('account.repairs.request') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">Book Repairs</flux:button>
            </div>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <flux:card class="p-6">
                    <div class="flex justify-between items-start gap-4 flex-wrap">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3 flex-wrap">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $booking->label }}</h3>
                                <flux:badge color="{{ match(strtolower((string) $booking->status)) {
                                    'confirmed', 'active' => 'green',
                                    'pending' => 'yellow',
                                    'completed', 'done', 'expired' => 'blue',
                                    'cancelled' => 'red',
                                    default => 'zinc'
                                } }}">
                                    {{ ucfirst((string) $booking->status) }}
                                </flux:badge>
                                <flux:badge>{{ $booking->type }}</flux:badge>
                            </div>
                            <div class="text-sm space-y-1 text-gray-600 dark:text-gray-400">
                                @if($booking->type === 'MOT')
                                    <p>Vehicle: <strong class="text-gray-900 dark:text-white">{{ $booking->source->vehicle_registration ?? 'N/A' }}</strong></p>
                                    <p>Date: <strong class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->source->date_of_appointment)->format('d M Y') }}</strong> at <strong class="text-gray-900 dark:text-white">{{ $booking->source->time_slot ?? 'N/A' }}</strong></p>
                                    <p>Branch: <strong class="text-gray-900 dark:text-white">{{ $booking->source->branch->name ?? 'N/A' }}</strong></p>
                                @else
                                    <p>Booking ID: <strong class="text-gray-900 dark:text-white">#{{ $booking->source->id }}</strong></p>
                                    <p>Start: <strong class="text-gray-900 dark:text-white">{{ $booking->source->start_date ? \Carbon\Carbon::parse($booking->source->start_date)->format('d M Y') : 'N/A' }}</strong></p>
                                    @php $activeItem = $booking->source->activeItems->first(); @endphp
                                    @if($activeItem && $activeItem->motorbike)
                                        <p>Bike: <strong class="text-gray-900 dark:text-white">{{ $activeItem->motorbike->make }} {{ $activeItem->motorbike->model }} ({{ $activeItem->motorbike->reg_no }})</strong></p>
                                    @endif
                                @endif
                            </div>
                            @if(!empty($booking->source->notes))
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Notes:</strong> {{ $booking->source->notes }}</p>
                                </div>
                            @endif
                            @if(!empty($booking->source->test_result))
                                <flux:callout
                                    variant="{{ $booking->source->test_result === 'pass' ? 'success' : 'danger' }}"
                                    class="mt-3">
                                    <flux:callout.text>Result: {{ strtoupper($booking->source->test_result) }}</flux:callout.text>
                                </flux:callout>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 flex-shrink-0">Booked: {{ \Carbon\Carbon::parse($booking->source->created_at)->format('d M Y') }}</p>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
