<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <flux:heading size="xl">Recovery Request #{{ $request->id }}</flux:heading>
        <flux:button href="{{ route('account.recovery.my-requests') }}" variant="outline">Back to My Requests</flux:button>
    </div>

    <flux:card class="p-6 space-y-4">
        <div class="flex items-center gap-3">
            <flux:badge color="{{ $request->is_dealt ? 'green' : 'yellow' }}">
                {{ $request->is_dealt ? 'Dealt' : 'Pending' }}
            </flux:badge>
            <p class="text-sm text-gray-500">Created {{ $request->created_at?->format('d M Y H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Pickup</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->pickup_postcode ?: '-' }}</p>
                <p class="text-gray-500">{{ $request->pickup_address ?: '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Dropoff</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->dropoff_postcode ?: '-' }}</p>
                <p class="text-gray-500">{{ $request->dropoff_address ?: '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Vehicle</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->vrm ?: '-' }}</p>
                <p class="text-gray-500">{{ $request->vehicle_type ?: '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Distance and Cost</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ number_format((float) $request->distance, 2) }} miles</p>
                <p class="text-gray-500">GBP {{ number_format((float) $request->total_cost, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Pickup Slot</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->pick_up_datetime ? \Carbon\Carbon::parse($request->pick_up_datetime)->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Branch</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->branch?->name ?: $request->branch_name ?: '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Customer</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $request->full_name ?: '-' }}</p>
                <p class="text-gray-500">{{ $request->email ?: '-' }} | {{ $request->phone ?: '-' }}</p>
                <p class="text-gray-500">{{ $request->customer_address ?: '-' }}</p>
            </div>
            @if($request->note)
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Note</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $request->note }}</p>
                </div>
            @endif
        </div>
    </flux:card>
</div>
