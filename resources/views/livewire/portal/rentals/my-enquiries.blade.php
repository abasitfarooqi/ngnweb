<div class="space-y-6">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">My Rental Enquiries</flux:heading>
        <flux:button href="{{ route('account.rentals') }}" variant="outline">Browse rentals</flux:button>
    </div>

    @if($enquiries->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="inbox" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400">No rental enquiries found yet.</p>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($enquiries as $enquiry)
                <flux:card class="p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap mb-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $enquiry->subject ?: 'Rental enquiry' }}
                            </h3>
                            <p class="text-xs text-gray-500">{{ $enquiry->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <flux:badge color="{{ in_array(strtolower((string) $enquiry->status), ['completed', 'done', 'closed'], true) ? 'green' : 'yellow' }}">
                            {{ ucfirst((string) ($enquiry->status ?: 'pending')) }}
                        </flux:badge>
                    </div>

                    @if($enquiry->description)
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->description }}</p>
                    @endif

                    <div class="mt-4">
                        @if($enquiry->conversation?->uuid)
                            <flux:button href="{{ route('account.support.thread', $enquiry->conversation->uuid) }}" variant="outline" size="sm">
                                Open chat
                            </flux:button>
                        @else
                            <flux:button href="{{ route('account.support') }}" variant="outline" size="sm">
                                Start chat about this enquiry
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $enquiries->links() }}
        </div>
    @endif
</div>
