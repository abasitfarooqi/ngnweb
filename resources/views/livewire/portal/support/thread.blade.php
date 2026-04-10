@php
    $latestMessageId = (int) ($messages->last()?->id ?? 0);
@endphp

<div class="space-y-4">
    <div
        id="support-thread-live-root"
        class="hidden"
        data-latest-url="{{ route('account.support.latest-message', ['conversationUuid' => $conversation->uuid]) }}"
        data-messages-html-url="{{ route('account.support.messages-html', ['conversationUuid' => $conversation->uuid]) }}"
        data-last-message-id="{{ $latestMessageId }}"
        data-conversation-uuid="{{ $conversation->uuid }}"
        data-customer-auth-id="{{ $customerAuthId }}"
    ></div>

    <div class="flex items-center justify-between gap-3">
        <div>
            <flux:heading size="xl">{{ $conversation->title ?: 'Support conversation' }}</flux:heading>
            <p class="mt-1 text-xs text-gray-500">
                Status: {{ ucfirst(str_replace('_', ' ', (string) $conversation->status)) }}
                @if($conversation->assignedBackpackUser)
                    · Handled by {{ $conversation->assignedBackpackUser->full_name ?: $conversation->assignedBackpackUser->name }}
                @endif
            </p>
        </div>
        <flux:button href="{{ route('account.support') }}" variant="outline" class="border-brand-red text-brand-red hover:bg-brand-red hover:text-white">
            Back to chats
        </flux:button>
    </div>

    <flux:callout variant="info" icon="information-circle">
        <flux:callout.text>We aim to reply within 24 hours. For urgent issues, please call branch support.</flux:callout.text>
    </flux:callout>

    <flux:card class="p-4">
        <div id="support-thread-messages-root" wire:ignore class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
            @include('portal.support.partials.thread-messages', ['messages' => $messages])
        </div>
    </flux:card>

    <flux:card class="p-4">
        <form wire:submit.prevent="sendMessage" class="space-y-3">
            <flux:field>
                <flux:label>Message</flux:label>
                <textarea
                    wire:model="messageBody"
                    rows="4"
                    class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100"
                    placeholder="Write your message..."
                    x-on:keydown.shift.enter.prevent="$el.closest('form')?.requestSubmit()"
                ></textarea>
                <p class="text-[11px] text-gray-500 mt-1">Tip: press Shift + Enter to send quickly.</p>
                @error('messageBody')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                @enderror
            </flux:field>
            <flux:field>
                <flux:label>Attachments (optional)</flux:label>
                <input type="file" wire:model="messageFiles" multiple class="block w-full text-sm text-gray-700 dark:text-gray-300" />
                <p class="text-xs text-gray-500 mt-1">Up to 5 files, 10MB each (jpg, png, webp, pdf, doc, docx, txt).</p>
                <div wire:loading wire:target="messageFiles" class="text-xs text-gray-500 mt-1">Uploading files…</div>
                @error('messageFiles')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                @enderror
                @error('messageFiles.*')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                @enderror
            </flux:field>
            <div class="flex justify-end">
                <flux:button type="submit" variant="filled" wire:loading.attr="disabled" wire:target="sendMessage,messageFiles" class="bg-brand-red text-white hover:bg-brand-red-dark px-6 py-2 font-semibold">
                    <span wire:loading.remove wire:target="sendMessage">Send</span>
                    <span wire:loading wire:target="sendMessage">Sending…</span>
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
