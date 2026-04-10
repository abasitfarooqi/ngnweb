@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SupportMessage> $messages */
@endphp
@forelse($messages as $message)
    <div class="{{ $message->sender_type === 'customer' ? 'text-right' : 'text-left' }}">
        <div class="inline-block max-w-[85%] border border-gray-200 dark:border-gray-700 p-3 text-sm {{ $message->sender_type === 'customer' ? 'bg-brand-red text-white border-brand-red' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }}">
            <p class="text-[11px] font-semibold mb-1 {{ $message->sender_type === 'customer' ? 'text-red-100' : 'text-gray-500' }}">
                {{ $message->sender_type === 'customer' ? 'You' : ($message->senderUser?->full_name ?: 'Support staff') }}
            </p>
            @if($message->body)
                <p class="whitespace-pre-line">{{ $message->body }}</p>
            @endif

            @if($message->attachments->isNotEmpty())
                <div class="mt-2 space-y-1 text-xs">
                    @foreach($message->attachments as $attachment)
                        <a href="{{ route('support.attachments.show', $attachment) }}" class="underline underline-offset-2" target="_blank" rel="noopener">
                            {{ $attachment->original_name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="text-[10px] mt-2 {{ $message->sender_type === 'customer' ? 'text-red-100' : 'text-gray-500' }}">
                {{ $message->created_at?->format('d M Y H:i') }}
            </p>
        </div>
    </div>
@empty
    <div class="text-sm text-gray-500">No messages yet. Send the first message.</div>
@endforelse
