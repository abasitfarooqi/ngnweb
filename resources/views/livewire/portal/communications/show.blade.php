<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('account.notifications') }}" class="text-sm text-brand-red hover:underline">← Notifications</a>
        <flux:button size="sm" variant="ghost" wire:click="archive" class="!rounded-none">Archive</flux:button>
    </div>

    <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <flux:heading size="lg">{{ $communication->title }}</flux:heading>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $communication->subject }}</p>
        <p class="mt-1 text-xs text-gray-400">{{ $communication->created_at?->format('d M Y H:i') }}</p>

        @if($enquiryAllowed)
            <div class="mt-4">
                @if($enquiryUuid)
                    <a href="{{ route('account.support.thread', $enquiryUuid) }}">
                        <flux:button size="sm" variant="primary" class="!rounded-none">Open enquiry</flux:button>
                    </a>
                @else
                    <flux:button size="sm" variant="primary" wire:click="startEnquiry" class="!rounded-none">Start enquiry from this message</flux:button>
                @endif
            </div>
        @endif
    </div>

    <div class="border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        @if($communication->content_html)
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! $communication->content_html !!}
            </div>
        @else
            <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $communication->content_text }}</p>
        @endif
    </div>
</div>
