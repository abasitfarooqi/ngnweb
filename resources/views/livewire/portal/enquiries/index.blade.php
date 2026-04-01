<div class="space-y-6">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">My Enquiries</flux:heading>
        <flux:button href="/" variant="outline">Back to site</flux:button>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach([
            'all' => 'All',
            'mot' => 'MOT',
            'rentals' => 'Rentals',
            'finance' => 'Finance',
            'shop' => 'Shop',
            'recovery' => 'Recovery',
            'ebike' => 'E-bike',
        ] as $key => $label)
            <button
                wire:click="setFilter('{{ $key }}')"
                class="px-3 py-1.5 text-xs border transition {{ $activeFilter === $key ? 'border-brand-red text-brand-red bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-brand-red' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if($enquiries->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="inbox" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400">No enquiries found yet.</p>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($enquiries as $enquiry)
                <flux:card class="p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap mb-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $enquiry->subject ?: ($enquiry->service_type ?: 'Customer enquiry') }}
                            </h3>
                            <p class="text-xs text-gray-500">{{ $enquiry->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:badge color="zinc">{{ str_replace('_', ' ', ucfirst($enquiry->enquiry_type)) }}</flux:badge>
                            <flux:badge color="{{ $enquiry->submission_context === 'authenticated_customer' ? 'green' : 'yellow' }}">
                                {{ $enquiry->submission_context === 'authenticated_customer' ? 'Signed in' : 'Guest' }}
                            </flux:badge>
                            <flux:badge color="{{ in_array(strtolower((string) $enquiry->status), ['completed', 'done', 'closed'], true) ? 'green' : 'yellow' }}">
                                {{ ucfirst((string) ($enquiry->status ?: 'pending')) }}
                            </flux:badge>
                        </div>
                    </div>

                    @if($enquiry->service_type)
                        <p class="text-xs text-gray-500 mb-2">Service: {{ $enquiry->service_type }}</p>
                    @endif

                    @if($enquiry->description)
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->description }}</p>
                    @endif
                </flux:card>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $enquiries->links() }}
        </div>
    @endif
</div>

