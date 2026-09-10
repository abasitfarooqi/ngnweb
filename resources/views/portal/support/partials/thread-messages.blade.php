@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SupportMessage> $messages */
@endphp
@forelse($messages as $message)
    <div data-message-id="message-{{ $message->id }}" class="flex {{ $message->sender_type === 'customer' ? 'justify-end' : 'justify-start' }}">
        <div class="flex max-w-[88%] items-end gap-2 {{ $message->sender_type === 'customer' ? 'flex-row-reverse' : '' }}">
            <div class="portal-chat-icon mb-1 flex size-7 shrink-0 items-center justify-center rounded-full border {{ $message->sender_type === 'customer' ? 'border-emerald-700/30 bg-emerald-600 text-white' : 'border-gray-300 bg-white text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200' }}"><flux:icon name="user" class="size-3.5" /></div>
            <div style="border-radius: 12px 12px {{ $message->sender_type === 'customer' ? '12px 2px' : '2px 12px' }};" class="portal-chat-bubble {{ $message->sender_type === 'customer' ? 'portal-chat-own bg-emerald-600 text-white' : 'portal-chat-other border border-gray-200 bg-white text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-white' }} min-w-0 overflow-hidden px-4 py-4 text-sm leading-5 shadow-md">
            <p class="!m-0 mb-1 text-[11px] font-semibold leading-4 opacity-70">
                {{ $message->sender_type === 'customer' ? 'You' : ($message->senderUser?->full_name ?: 'Support staff') }}
            </p>
            @if($message->body)
                <div class="flex items-start gap-2">
                    <p class="!m-0 flex-1 whitespace-pre-line">{{ $message->body }}</p>
                    <button type="button" class="!m-0 text-[10px] leading-4 underline opacity-70 hover:opacity-100" onclick="navigator.clipboard?.writeText(@js($message->body))">Copy</button>
                </div>
            @endif
            @if($message->reply_to_message_id)
                <button type="button" class="mb-1 text-[11px] underline opacity-80" onclick="document.querySelector('[data-message-id=\'message-{{ $message->reply_to_message_id }}\']')?.scrollIntoView({behavior:'smooth',block:'center'})">Reply to #{{ $message->reply_to_message_id }}</button>
            @endif

            @if($message->attachments->isNotEmpty())
                <div style="border-radius: 15px;" class="-mx-1 mt-3 space-y-2 rounded-2xl border p-1.5 {{ $message->sender_type === 'customer' ? 'border-white/25 bg-white/15' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/80' }}">
                    @foreach($message->attachments as $attachment)
                        <a style="border-radius: 11px;" href="{{ route('support.attachments.show', $attachment) }}" class="flex min-w-0 items-center gap-3 rounded-xl border px-3 py-2.5 no-underline transition hover:opacity-80 {{ $message->sender_type === 'customer' ? 'border-white/25 bg-white/10' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900' }}" target="_blank" rel="noopener">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $message->sender_type === 'customer' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800' }}"><flux:icon name="paper-clip" class="size-4" /></span>
                            <span class="min-w-0 truncate font-medium underline-offset-2 hover:underline">{{ $attachment->original_name }}</span>
                            <flux:icon name="arrow-down-tray" class="ml-auto size-4 shrink-0 opacity-70" />
                        </a>
                    @endforeach
                </div>
            @endif

            @if($message->sender_type === 'customer')
                <form method="POST" action="{{ route('account.support.delete-message', [$message->conversation->uuid, $message->id]) }}" class="mt-2" onsubmit="return confirm('Hide this message for everyone?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="!m-0 text-[10px] leading-4 underline opacity-70">Delete</button>
                </form>
            @endif

            <p class="!m-0 mt-1.5 flex items-center justify-end gap-1 text-[10px] leading-4 {{ $message->sender_type === 'customer' ? 'text-emerald-100' : 'text-gray-500' }}">
                {{ $message->created_at?->format('H:i') }}
                @if($message->sender_type === 'customer') <span class="text-[11px] tracking-[-3px]">✓✓</span> @endif
            </p>
            </div>
        </div>
    </div>
@empty
    <div class="text-sm text-gray-500">No messages found.</div>
@endforelse
