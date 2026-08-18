<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <flux:heading size="xl">{{ $communication->title }}</flux:heading>
            <flux:text class="mt-1 font-mono text-xs">{{ $communication->uuid }}</flux:text>
            <flux:text class="mt-1 text-sm">{{ $communication->recipient_email ?: 'No customer email captured' }}</flux:text>
        </div>
        <a href="{{ route('flux-admin.communications.sent.index') }}">
            <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Back</flux:button>
        </a>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
            <flux:heading size="lg">Stored snapshot</flux:heading>
            <flux:text class="mt-1">{{ $communication->subject }}</flux:text>
            <div class="mt-4 border border-zinc-200 bg-white p-4 dark:border-zinc-700">
                @if($communication->content_html)
                    {!! $communication->content_html !!}
                @else
                    <p class="whitespace-pre-wrap text-sm">{{ $communication->content_text }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading size="lg">Delivery</flux:heading>
                <dl class="mt-3 space-y-3 text-sm">
                    @forelse($communication->deliveries as $delivery)
                        <div>
                            <dt class="font-medium text-zinc-900 dark:text-white">{{ str_replace('_', ' ', $delivery->channel) }}</dt>
                            <dd class="mt-1 text-zinc-600 dark:text-zinc-400">
                                {{ $delivery->status }}
                                @if($delivery->sent_at) · sent {{ $delivery->sent_at->format('d M Y H:i') }} @endif
                                @if($delivery->delivered_at) · delivered {{ $delivery->delivered_at->format('d M Y H:i') }} @endif
                                @if($delivery->failure_reason) · {{ $delivery->failure_reason }} @endif
                            </dd>
                        </div>
                    @empty
                        <p class="text-zinc-500">No delivery rows.</p>
                    @endforelse
                </dl>
            </div>

            <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading size="lg">Customer</flux:heading>
                @forelse($communication->recipients as $recipient)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Portal user #{{ $recipient->customer_auth_id }}
                        · seen {{ $recipient->seen_at?->format('d M Y H:i') ?? '—' }}
                        · read {{ $recipient->read_at?->format('d M Y H:i') ?? '—' }}
                    </p>
                @empty
                    <p class="mt-2 text-sm text-zinc-500">No portal recipient. Inbox was off, or no matching portal account.</p>
                @endforelse
                @if(data_get($communication->payload_snapshot, 'enquiry_conversation_uuid'))
                    <p class="mt-3 text-sm">Enquiry started: {{ data_get($communication->payload_snapshot, 'enquiry_conversation_uuid') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
