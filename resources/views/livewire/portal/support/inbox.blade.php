<div class="space-y-6" wire:poll.5s="$refresh">
    <div class="flex items-center justify-between gap-3">
        <div>
            <flux:heading size="xl">Conversations</flux:heading>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Start a new chat or continue any general and enquiry conversation history in one place.</p>
        </div>
        <flux:button href="{{ route('account.support.start-general') }}" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark px-5 py-2 font-semibold shadow-sm">
            Start general chat
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <flux:card class="p-5 lg:col-span-2">
            <div class="flex flex-col md:flex-row md:items-end gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Search</label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm bg-white dark:bg-gray-900"
                        placeholder="Search title, enquiry, keyword"
                    />
                </div>
                <div class="w-full md:w-52">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Type</label>
                    <select wire:model.live="typeFilter" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm bg-white dark:bg-gray-900">
                        <option value="all">All</option>
                        <option value="general">General</option>
                        <option value="enquiry">Enquiry</option>
                    </select>
                </div>
                <div class="w-full md:w-52">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm bg-white dark:bg-gray-900">
                        <option value="all">All</option>
                        <option value="open">Open</option>
                        <option value="waiting_for_staff">Waiting for staff</option>
                        <option value="waiting_for_customer">Waiting for customer</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            @if($conversations->isEmpty())
                <div class="text-sm text-gray-500">No chats yet. Start a general chat or open one from an enquiry.</div>
            @else
                <div class="space-y-3">
                    @foreach($conversations as $conversation)
                        <a href="{{ route('account.support.thread', $conversation->uuid) }}" class="block border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-red transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $conversation->title ?: 'Conversation #'.$conversation->id }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $conversation->service_booking_id ? 'Enquiry conversation' : 'General conversation' }}
                                        @if($conversation->topic)
                                            · {{ $conversation->topic }}
                                        @endif
                                    </p>
                                    @if($conversation->serviceBooking)
                                        <p class="text-xs text-gray-500 mt-1">Linked enquiry #{{ $conversation->serviceBooking->id }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <flux:badge color="{{ in_array($conversation->status, ['resolved', 'closed'], true) ? 'green' : 'yellow' }}">
                                        {{ ucfirst(str_replace('_', ' ', (string) $conversation->status)) }}
                                    </flux:badge>
                                    <p class="text-[11px] text-gray-500 mt-2">{{ $conversation->last_message_at?->diffForHumans() ?: 'No messages yet' }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </flux:card>

        <flux:card class="p-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Start from enquiry</h3>
            @if($recentEnquiries->isEmpty())
                <div class="text-sm text-gray-500">No enquiries yet.</div>
            @else
                <div class="space-y-2">
                    @foreach($recentEnquiries as $enquiry)
                        <a
                            href="{{ route('account.support.from-enquiry', ['serviceBookingId' => $enquiry->id]) }}"
                            class="block w-full text-left border border-gray-200 dark:border-gray-700 p-3 hover:border-brand-red hover:bg-red-50/30 dark:hover:bg-red-950/20 transition"
                        >
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $enquiry->service_type ?: 'Enquiry #'.$enquiry->id }}</p>
                            <p class="text-xs text-gray-500">Submitted {{ $enquiry->created_at?->format('d M Y H:i') }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </flux:card>
    </div>
</div>

