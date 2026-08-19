<div class="space-y-6" wire:poll.1500ms="$refresh">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <flux:heading size="xl">{{ $communication->title }}</flux:heading>
            <flux:text class="mt-1 font-mono text-xs">{{ $communication->uuid }}</flux:text>
            @unless($canManageCommunications)
                <p class="mt-2 text-xs text-zinc-500">Read only. Replies and enquiry chat are for staff with communications rights.</p>
            @endunless
        </div>
        <a href="{{ route('flux-admin.communications.sent.index') }}">
            <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Back</flux:button>
        </a>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
            <flux:heading size="lg">Stored snapshot</flux:heading>
            <flux:text class="mt-1">{{ $communication->subject }}</flux:text>
            <div class="mt-4">
                <x-communication-email-snapshot :html="$communication->content_html" />
            </div>

            @php
                $mailAttachments = $communication->attachments->filter(fn ($file) => data_get($file->metadata, 'source') !== 'reply');
                $replyAttachments = $communication->attachments->filter(fn ($file) => data_get($file->metadata, 'source') === 'reply')->groupBy(fn ($file) => (int) data_get($file->metadata, 'reply_id'));
            @endphp

            @if($mailAttachments->isNotEmpty())
                <div class="mt-4">
                    <flux:heading size="sm">Attachments</flux:heading>
                    <ul class="mt-2 space-y-2">
                        @foreach($mailAttachments as $attachment)
                            <li>
                                <a href="{{ route('flux-admin.communications.sent.attachments.show', ['communication' => $communication->uuid, 'attachment' => $attachment->uuid]) }}" class="text-sm text-brand-red hover:underline">
                                    {{ $attachment->display_name ?: $attachment->filename }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6">
                <flux:heading size="sm">Replies</flux:heading>
                <div class="mt-3 space-y-3" wire:key="sent-replies-{{ $realtimeTick }}-{{ $communication->replies->count() }}">
                    @forelse($communication->replies as $reply)
                        <div class="border border-zinc-200 p-3 dark:border-zinc-700">
                            <p class="text-xs text-zinc-500">{{ $reply->authorLabel() }} · {{ $reply->created_at?->format('d M Y H:i') }}</p>
                            <p class="mt-1 whitespace-pre-wrap text-sm">{{ $reply->body }}</p>
                            @foreach($replyAttachments->get($reply->id, collect()) as $file)
                                <p class="mt-2">
                                    <a href="{{ route('flux-admin.communications.sent.attachments.show', ['communication' => $communication->uuid, 'attachment' => $file->uuid]) }}" class="text-sm text-brand-red hover:underline">{{ $file->display_name ?: $file->filename }}</a>
                                </p>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">No replies yet.</p>
                    @endforelse
                </div>

                @if($replyAllowed)
                    <form wire:submit="sendReply" class="mt-4 space-y-3">
                        <textarea wire:model="replyBody" rows="4" class="w-full border border-zinc-300 bg-white p-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="Write a reply on this communication"></textarea>
                        @error('replyBody') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                        <div>
                            <p class="text-sm">Attachments (optional)</p>
                            <input type="file" wire:model="replyFiles" multiple class="mt-1 block w-full text-sm">
                            <p class="mt-1 text-xs text-zinc-500">Up to 5 files, 10MB each. Types allowed: JPG, PNG, WebP, PDF, Word, plain text.</p>
                            @error('replyFiles') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                            @error('replyFiles.*') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <flux:button type="submit" size="sm" variant="primary" class="!rounded-none">Send</flux:button>
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading size="lg">Enquiry chat</flux:heading>
                @if($enquiry)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Status: {{ str_replace('_', ' ', (string) $enquiry->status) }}
                        @if($enquiryOpen) · open until dealt @endif
                    </p>
                    <a href="{{ route('flux-admin.support-inbox.index', ['c' => $enquiry->id]) }}" class="mt-3 inline-block">
                        <flux:button size="sm" variant="primary" class="!rounded-none">Open enquiry chat</flux:button>
                    </a>
                @elseif($canStartEnquiry)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">No enquiry chat has been started from this message yet.</p>
                    <flux:button size="sm" variant="primary" wire:click="startEnquiry" class="mt-3 !rounded-none">Start enquiry chat</flux:button>
                @elseif($canManageCommunications)
                    <p class="mt-2 text-sm text-zinc-500">No portal account is linked, so a support chat cannot be started yet.</p>
                @else
                    <p class="mt-2 text-sm text-zinc-500">Read only. Enquiry chat cannot be started from this page.</p>
                @endif
            </div>

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
                                @if($delivery->provider_message_id) · {{ $delivery->provider_message_id }} @endif
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
                        · {{ $recipient->archived_at ? 'archived' : 'in inbox' }}
                    </p>
                @empty
                    <p class="mt-2 text-sm text-zinc-500">No portal recipient yet. Inbox is waiting for a matching portal account, or Inbox was off.</p>
                @endforelse
                @if(data_get($communication->payload_snapshot, 'legacy_email_fallback'))
                    <p class="mt-3 text-sm text-zinc-500">Email was sent as a no-portal fallback.</p>
                @endif
            </div>
        </div>
    </div>
</div>
