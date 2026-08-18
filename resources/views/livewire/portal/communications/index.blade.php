<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Notifications</flux:heading>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Messages from NGN Motors. Email copies still go to your inbox when Email is enabled.
            </p>
            <button type="button" id="enable-communication-alerts" class="mt-2 text-sm text-brand-red hover:underline">
                Enable browser alerts
            </button>
            <p id="portal-browser-alerts-status" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
        </div>
        <div class="flex gap-2">
            <flux:button size="sm" variant="{{ $archived ? 'ghost' : 'primary' }}" wire:click="showInbox" class="!rounded-none">Inbox</flux:button>
            <flux:button size="sm" variant="{{ $archived ? 'primary' : 'ghost' }}" wire:click="showArchived" class="!rounded-none">Archived</flux:button>
        </div>
    </div>

    @if($unread > 0 && ! $archived)
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $unread }} unread</p>
    @endif

    @if($communications->isEmpty())
        <div class="border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800">
            <flux:icon name="bell" class="mx-auto mb-3 h-12 w-12 text-gray-400" />
            <p class="text-gray-600 dark:text-gray-400">
                {{ $archived ? 'No archived notifications.' : 'No notifications yet.' }}
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($communications as $communication)
                @php($recipient = $communication->recipients->first())
                <a href="{{ route('account.notifications.show', $communication->uuid) }}" class="block border border-gray-200 bg-white p-5 hover:border-brand-red dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $communication->title }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $communication->preview }}</p>
                            <p class="mt-2 text-xs text-gray-400">{{ $communication->created_at?->format('d M Y H:i') }}</p>
                            @if((int) $communication->attachments_count > 0)
                                <p class="mt-1 text-xs text-gray-500">{{ $communication->attachments_count }} attachment{{ (int) $communication->attachments_count === 1 ? '' : 's' }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @if($recipient?->read_at === null)
                                <flux:badge color="red">Unread</flux:badge>
                            @endif
                            @if($archived)
                                <flux:button size="xs" variant="ghost" wire:click.prevent="unarchive({{ $communication->id }})" class="!rounded-none">Unarchive</flux:button>
                            @else
                                <flux:button size="xs" variant="ghost" wire:click.prevent="archive({{ $communication->id }})" class="!rounded-none">Archive</flux:button>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-4">{{ $communications->links() }}</div>
    @endif
</div>
