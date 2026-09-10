@php
    $latestMessageId = (int) ($messages->last()?->id ?? 0);
@endphp

<div class="portal-support-thread-page mx-auto w-full max-w-5xl">
<style>
    .portal-chat-wallpaper {
        position: relative;
        isolation: isolate;
        background-color: #edf1ef;
    }
    .portal-chat-wallpaper::before { content: ''; position: absolute; inset: 0; z-index: -1; background: url('{{ asset('img/watermark.png') }}') center / 520px auto repeat; opacity: .045; mix-blend-mode: multiply; pointer-events: none; }
    .dark .portal-chat-wallpaper { background-color: #20262d; }
    .dark .portal-chat-wallpaper::before { opacity: .035; filter: grayscale(1) brightness(1.5); mix-blend-mode: screen; }
    .portal-chat-bubble { position: relative; }
    .portal-chat-bubble::after { content: ''; position: absolute; bottom: 0; width: 0; height: 0; border-style: solid; }
    .portal-chat-bubble.portal-chat-own::after { right: -8px; border-width: 0 0 10px 10px; border-color: transparent transparent #059669 transparent; }
    .portal-chat-bubble.portal-chat-other::after { left: -8px; border-width: 10px 10px 0 0; border-color: #ffffff transparent transparent transparent; }
    .dark .portal-chat-bubble.portal-chat-other::after { border-color: #111827 transparent transparent transparent; }
    .portal-chat-icon { box-shadow: 0 1px 3px rgba(15, 23, 42, .18); }
</style>
    <div
        id="support-thread-live-root"
        class="hidden"
        data-latest-url="{{ route('account.support.latest-message', ['conversationUuid' => $conversation->uuid]) }}"
        data-messages-html-url="{{ route('account.support.messages-html', ['conversationUuid' => $conversation->uuid]) }}"
        data-last-message-id="{{ $latestMessageId }}"
        data-conversation-uuid="{{ $conversation->uuid }}"
        data-customer-auth-id="{{ $customerAuthId }}"
    ></div>

    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:gap-4 sm:p-5">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-brand-red text-white"><flux:icon name="chat-bubble-left-right" class="size-5" /></div>
            <div class="min-w-0">
            <flux:heading size="lg" class="truncate">{{ $conversation->title ?: 'Support conversation' }}</flux:heading>
            <p class="mt-1 text-xs text-gray-500">
                Status: {{ ucfirst(str_replace('_', ' ', (string) $conversation->status)) }}
                @if(str_starts_with((string) $conversation->topic, 'Notification:'))
                    · From notification
                    @if(! empty($notificationOpen)) · open until dealt @endif
                @endif
                @if($conversation->assignedBackpackUser)
                    · Handled by {{ $conversation->assignedBackpackUser->full_name ?: $conversation->assignedBackpackUser->name }}
                @endif
            </p>
        </div>
        </div>
        <a href="{{ route('account.support') }}" class="inline-flex shrink-0 items-center gap-2 border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-emerald-400 dark:hover:bg-gray-700">
            <flux:icon name="arrow-left" class="size-4" />
            Back to chats
        </a>
    </div>

    <div class="shrink-0">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search messages in this chat…" />
    </div>

    @if(! empty($notificationUuid))
        <p class="text-sm text-gray-600 dark:text-gray-400">
            This chat is about a notification.
            <a href="{{ route('account.notifications.show', $notificationUuid) }}" class="text-brand-red hover:underline">Open the original message</a>
        </p>
    @endif

    <div class="portal-support-thread-card overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex shrink-0 items-center gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3 text-xs font-medium text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300"><flux:icon name="lock-closed" class="size-4" /> Secure NGN support chat</div>
        <div id="support-thread-messages-root" data-support-chat-wall="true" wire:ignore class="portal-support-thread-wall portal-chat-wallpaper space-y-3 p-4 sm:p-6">
            @include('portal.support.partials.thread-messages', ['messages' => $messages])
        </div>
        <form
            id="support-thread-composer"
            action="{{ route('account.support.send-message', ['conversationUuid' => $conversation->uuid]) }}"
            method="POST"
            enctype="multipart/form-data"
            class="shrink-0 space-y-2 border-t border-gray-200 p-2 dark:border-gray-800 sm:p-4"
        >
            @csrf
            <input type="hidden" name="reply_to_message_id" value="{{ old('reply_to_message_id') }}">
            <div class="flex items-end gap-2">
                <div class="min-w-0 flex-1">
                <textarea
                    name="body"
                    rows="1"
                    class="h-12 w-full resize-none border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-brand-red focus:ring-2 focus:ring-brand-red/10 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100 dark:placeholder:text-gray-500"
                    placeholder="Write your message..."
                >{{ old('body') }}</textarea>
                @error('body')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                @enderror
                <p id="support-thread-composer-error" class="mt-1 hidden text-xs text-red-600 dark:text-red-400"></p>
                </div>
                <label class="flex h-12 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border border-gray-300 bg-gray-50 px-4 text-xs font-medium text-gray-600 transition hover:border-brand-red hover:text-brand-red dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300">
                    <flux:icon name="paper-clip" class="size-4" /> <span class="hidden sm:inline">Attach</span>
                    <input type="file" name="files[]" multiple class="sr-only" accept="image/jpeg,image/png,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,video/mp4,video/quicktime,video/webm" />
                </label>
                <flux:button type="submit" variant="filled" icon="paper-airplane" class="!m-0 size-12 shrink-0 rounded-full bg-brand-red p-0 text-white shadow-sm hover:bg-brand-red-dark" aria-label="Send message"></flux:button>
            </div>
            <p class="text-[11px] text-gray-500">Up to 5 files, 100MB each · images, documents, or video.</p>
            @error('files')
                <div class="text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
            @error('files.*')
                <div class="text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </form>
    </div>
</div>
