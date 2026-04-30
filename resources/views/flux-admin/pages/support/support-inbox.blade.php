<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <flux:heading size="xl">Support inbox</flux:heading>
            <flux:text class="mt-1">Read customer messages, reply inline, assign and set status.</flux:text>
        </div>
        <div class="flex gap-2 items-center">
            <flux:select wire:model.live="statusFilter" class="min-w-[10rem]">
                <flux:select.option value="all">All statuses</flux:select.option>
                <flux:select.option value="open">Open</flux:select.option>
                <flux:select.option value="waiting_for_staff">Waiting for staff</flux:select.option>
                <flux:select.option value="awaiting_customer">Awaiting customer</flux:select.option>
                <flux:select.option value="resolved">Resolved</flux:select.option>
                <flux:select.option value="closed">Closed</flux:select.option>
            </flux:select>
            <flux:input wire:model.live.debounce.400ms="search" placeholder="Search title/topic…" class="min-w-[12rem]" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[22rem,1fr] gap-4 h-[calc(100vh-14rem)] min-h-[32rem]">
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-y-auto">
            @forelse($conversations as $c)
                <button type="button" wire:click="selectConversation({{ $c->id }})" wire:key="conv-{{ $c->id }}"
                    class="w-full text-left p-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 {{ $selectedConversationId === $c->id ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-zinc-900 dark:text-white truncate">{{ $c->title ?: 'Conversation #'.$c->id }}</div>
                            <div class="text-xs text-zinc-500 truncate">{{ $c->customerAuth?->email ?: 'Unknown customer' }}</div>
                        </div>
                        @if($c->unread_customer_count > 0)
                            <span class="shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 bg-red-500 text-white text-xs font-bold">{{ $c->unread_customer_count }}</span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">{{ str_replace('_', ' ', $c->status ?? '—') }}</span>
                        @if($c->assignedBackpackUser)
                            <span class="text-zinc-500">→ {{ $c->assignedBackpackUser->name }}</span>
                        @endif
                        @if($c->last_message_at)
                            <span class="ml-auto text-zinc-400">{{ \Carbon\Carbon::parse($c->last_message_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if($c->latestMessage)
                        <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2">{{ Str::limit(strip_tags((string) $c->latestMessage->body), 80) }}</div>
                    @endif
                </button>
            @empty
                <div class="p-6 text-center text-zinc-500">No conversations.</div>
            @endforelse
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex flex-col min-h-0">
            @if($selected)
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-3 flex-wrap">
                    <div class="min-w-0">
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $selected->title ?: 'Conversation #'.$selected->id }}</div>
                        <div class="text-xs text-zinc-500">
                            {{ $selected->customerAuth?->email }} ·
                            <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800">{{ str_replace('_', ' ', $selected->status ?? '—') }}</span>
                            @if($selected->assignedBackpackUser) · assigned to {{ $selected->assignedBackpackUser->name }} @endif
                        </div>
                    </div>
                    <div class="flex gap-1 flex-wrap">
                        <flux:button size="xs" variant="ghost" wire:click="assignToMe" icon="user" class="!rounded-none">Assign to me</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('awaiting_customer')" class="!rounded-none">Awaiting customer</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('resolved')" icon="check-circle" class="!rounded-none">Resolve</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('closed')" icon="x-circle" class="!rounded-none">Close</flux:button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0">
                    @foreach($selected->messages->sortBy('id') as $m)
                        <div class="flex {{ $m->sender_type === 'staff' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $m->id }}">
                            <div class="max-w-[70%] px-3 py-2 text-sm {{ $m->sender_type === 'staff' ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' }}">
                                <div class="whitespace-pre-wrap">{{ $m->body }}</div>
                                <div class="mt-1 text-[10px] opacity-70">
                                    {{ $m->sender_type === 'staff' ? ($m->senderUser?->name ?: 'Staff') : 'Customer' }}
                                    · {{ $m->created_at?->format('d M H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit.prevent="sendMessage" class="p-3 border-t border-zinc-200 dark:border-zinc-800 flex gap-2 items-end">
                    <flux:textarea wire:model="newMessage" rows="2" placeholder="Type a reply…" class="flex-1" />
                    <flux:button type="submit" variant="primary" icon="paper-airplane" class="!rounded-none">Send</flux:button>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center text-zinc-500">
                    <div class="text-center">
                        <flux:icon name="inbox" class="size-12 mx-auto opacity-40" />
                        <div class="mt-3">Pick a conversation from the left.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
