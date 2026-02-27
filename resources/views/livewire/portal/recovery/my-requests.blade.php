<div class="space-y-6">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">My Recovery Requests</flux:heading>
        <flux:button href="{{ route('account.recovery.request') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
            New Request
        </flux:button>
    </div>

    @if($requests->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="truck" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400 mb-4">You haven't requested any recovery services yet.</p>
            <flux:button href="{{ route('account.recovery.request') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                Request Recovery
            </flux:button>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($requests as $request)
                <flux:card class="p-6">
                    <div class="flex justify-between items-start gap-4 flex-wrap">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $request->urgency === 'urgent' ? 'Urgent' : 'Planned' }} Recovery
                                </h3>
                                <flux:badge color="{{ match($request->status) {
                                    'requested' => 'yellow',
                                    'confirmed' => 'blue',
                                    'dispatched' => 'purple',
                                    'collected', 'delivered' => 'green',
                                    'completed' => 'zinc',
                                    default => 'zinc'
                                } }}">
                                    {{ ucfirst($request->status) }}
                                </flux:badge>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Pickup</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $request->pickup_postcode }}</p>
                                    <p class="text-gray-500">{{ $request->pickup_address }}</p>
                                </div>
                                @if($request->reg_no)
                                    <div>
                                        <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Bike</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $request->reg_no }}</p>
                                        @if($request->make || $request->model)
                                            <p class="text-gray-500">{{ $request->make }} {{ $request->model }}</p>
                                        @endif
                                    </div>
                                @endif
                                <div>
                                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Issue</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($request->issue_type ?? 'Not specified')) }}</p>
                                    @if($request->issue_description)
                                        <p class="text-gray-500">{{ $request->issue_description }}</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Destination</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($request->destination_type) }}</p>
                                    @if($request->branch)
                                        <p class="text-gray-500">{{ $request->branch->name }}</p>
                                    @elseif($request->destination_address)
                                        <p class="text-gray-500">{{ $request->destination_address }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 flex-shrink-0">{{ $request->created_at->format('d M Y H:i') }}</p>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
