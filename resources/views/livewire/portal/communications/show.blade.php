<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('account.notifications') }}" class="text-sm text-brand-red hover:underline">← Notifications</a>
        @if($recipient->archived_at)
            <flux:button size="sm" variant="ghost" wire:click="unarchive" class="!rounded-none">Unarchive</flux:button>
        @else
            <flux:button size="sm" variant="ghost" wire:click="archive" class="!rounded-none">Archive</flux:button>
        @endif
    </div>

    <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <flux:heading size="lg">{{ $communication->title }}</flux:heading>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $communication->subject }}</p>
        <p class="mt-1 text-xs text-gray-400">{{ $communication->created_at?->format('d M Y H:i') }}</p>

        <div class="mt-4 flex flex-wrap gap-2">
            @if($enquiry)
                <a href="{{ route('account.support.thread', $enquiry->uuid) }}">
                    <flux:button size="sm" variant="primary" class="!rounded-none">
                        {{ $enquiryOpen ? 'Open enquiry chat' : 'View enquiry chat' }}
                    </flux:button>
                </a>
                <span class="self-center text-xs text-gray-500 dark:text-gray-400">
                    Chat status: {{ str_replace('_', ' ', (string) $enquiry->status) }}
                    @if($enquiryOpen) · open until dealt @endif
                </span>
            @else
                <flux:button size="sm" variant="primary" wire:click="startEnquiry" class="!rounded-none">Start enquiry chat about this message</flux:button>
            @endif
        </div>
    </div>

    <x-communication-email-snapshot :html="$communication->content_html" />

    @if($communication->content_html === '' && $communication->content_text)
        <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <p class="whitespace-pre-wrap text-center text-sm text-gray-700 dark:text-gray-300">{{ $communication->content_text }}</p>
        </div>
    @endif

    @php
        $mailAttachments = $communication->attachments->filter(fn ($file) => data_get($file->metadata, 'source') !== 'reply');
        $replyAttachments = $communication->attachments->filter(fn ($file) => data_get($file->metadata, 'source') === 'reply')->groupBy(fn ($file) => (int) data_get($file->metadata, 'reply_id'));
    @endphp

    @if($mailAttachments->isNotEmpty())
        <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <flux:heading size="sm">Attachments</flux:heading>
            <ul class="mt-3 space-y-2">
                @foreach($mailAttachments as $attachment)
                    <li>
                        <a href="{{ route('account.notifications.attachments.show', ['uuid' => $communication->uuid, 'attachment' => $attachment->uuid]) }}" class="text-sm text-brand-red hover:underline">
                            {{ $attachment->display_name ?: $attachment->filename }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($communication->replies->isNotEmpty() || $replyAllowed)
        <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <flux:heading size="sm">Replies</flux:heading>
            <div class="mt-3 space-y-3">
                @forelse($communication->replies as $reply)
                    <div class="border border-gray-100 p-3 dark:border-gray-700">
                        <p class="text-xs text-gray-400">{{ $reply->authorLabel() }} · {{ $reply->created_at?->format('d M Y H:i') }}</p>
                        <p class="mt-1 whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">{{ $reply->body }}</p>
                        @foreach($replyAttachments->get($reply->id, collect()) as $file)
                            <p class="mt-2">
                                <a href="{{ route('account.notifications.attachments.show', ['uuid' => $communication->uuid, 'attachment' => $file->uuid]) }}" class="text-sm text-brand-red hover:underline">{{ $file->display_name ?: $file->filename }}</a>
                            </p>
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No replies yet.</p>
                @endforelse
            </div>

            @if($replyAllowed)
                <form wire:submit="sendReply" class="mt-4 space-y-3">
                    <textarea wire:model="replyBody" rows="4" class="w-full border border-gray-300 bg-white p-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" placeholder="Write a reply"></textarea>
                    @error('replyBody') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Attachments (optional)</p>
                        <input type="file" wire:model="replyFiles" multiple class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Up to 5 files, 10MB each. Types allowed: JPG, PNG, WebP, PDF, Word, plain text.</p>
                        @error('replyFiles') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                        @error('replyFiles.*') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                    </div>
                    <flux:button type="submit" size="sm" variant="primary" class="!rounded-none">Send</flux:button>
                </form>
            @endif
        </div>
    @endif
</div>
