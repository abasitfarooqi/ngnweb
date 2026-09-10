<style>
    .flux-chat-wallpaper { position: relative; isolation: isolate; overflow: hidden; background-color: #f0f2f1; }
    .flux-chat-wallpaper::before { content: ''; position: absolute; inset: 0; z-index: -1; background: url('{{ asset('img/watermark.png') }}') center / 520px auto repeat; opacity: .04; mix-blend-mode: multiply; pointer-events: none; }
    .dark .flux-chat-wallpaper { background-color: #20262d; }
    .dark .flux-chat-wallpaper::before { opacity: .03; filter: grayscale(1) brightness(1.5); mix-blend-mode: screen; }
    .flux-chat-bubble { position: relative; }
    .flux-chat-bubble::after { content: ''; position: absolute; bottom: 0; width: 0; height: 0; border-style: solid; }
    .flux-chat-bubble.flux-chat-own::after { right: -8px; border-width: 0 0 10px 10px; border-color: transparent transparent #09090b transparent; }
    .flux-chat-bubble.flux-chat-other::after { left: -8px; border-width: 10px 10px 0 0; border-color: #f4f4f5 transparent transparent transparent; }
    .dark .flux-chat-bubble.flux-chat-other::after { border-color: #27272a transparent transparent transparent; }
</style>
<div class="space-y-6" wire:poll.visible.5s="refreshRealtimeState">
    <div id="support-admin-live-root" class="hidden"></div>

    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                <flux:icon name="chat-bubble-left-right" class="size-6" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl">Support inbox</flux:heading>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-[11px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><span class="size-1.5 rounded-full bg-emerald-500"></span>Live</span>
                </div>
                <flux:text class="mt-1">One secure conversation per customer.</flux:text>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <flux:select wire:model.live="statusFilter" class="min-w-[11rem] rounded-xl">
                <flux:select.option value="all">All statuses</flux:select.option>
                <flux:select.option value="open">Open</flux:select.option>
                <flux:select.option value="waiting_for_staff">Waiting for staff</flux:select.option>
                <flux:select.option value="awaiting_customer">Awaiting customer</flux:select.option>
                <flux:select.option value="resolved">Resolved</flux:select.option>
                <flux:select.option value="closed">Closed</flux:select.option>
            </flux:select>
            <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" placeholder="Search conversations…" class="min-w-[15rem]" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[22rem,1fr] gap-4 h-[calc(100vh-14rem)] min-h-[32rem]">
        <div class="overflow-y-auto rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @forelse($conversations as $c)
                <button type="button" wire:click="selectConversation({{ $c->id }})" wire:key="conv-{{ $c->id }}"
                    class="w-full border-b border-zinc-100 p-4 text-left transition hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/60 {{ $selectedConversationId === $c->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 font-semibold text-zinc-900 dark:text-white truncate"><span class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="user" class="size-4" /></span><span class="truncate">{{ $c->title ?: 'Conversation #'.$c->id }}</span></div>
                            <div class="text-xs text-zinc-500 truncate">{{ $c->customerAuth?->email ?: 'Unknown customer' }}</div>
                            @if(str_starts_with((string) $c->topic, 'Notification:'))
                                <div class="text-xs text-brand-red mt-0.5 truncate">From notification · open until dealt</div>
                            @endif
                        </div>
                        @if($c->unread_customer_count > 0)
                            <span class="shrink-0 inline-flex min-w-6 items-center justify-center rounded-full bg-zinc-950 px-2 py-1 text-xs font-bold text-white dark:bg-white dark:text-zinc-950">{{ $c->unread_customer_count }}</span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ str_replace('_', ' ', $c->status ?? '—') }}</span>
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

        <div class="flex min-h-0 flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @if($selected)
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 p-5 dark:border-zinc-800">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 font-semibold text-zinc-900 dark:text-white"><span class="flex size-9 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950"><flux:icon name="user" class="size-4" /></span>{{ $selected->title ?: 'Conversation #'.$selected->id }}</div>
                        <div class="text-xs text-zinc-500">
                            {{ $selected->customerAuth?->email }} ·
                            <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800">{{ str_replace('_', ' ', $selected->status ?? '—') }}</span>
                            @if(str_starts_with((string) $selected->topic, 'Notification:'))
                                · From notification{{ ! in_array((string) $selected->status, ['resolved', 'closed'], true) ? ' · open until dealt' : '' }}
                            @elseif($selected->topic)
                                · {{ $selected->topic }}
                            @endif
                            @if($selected->assignedBackpackUser) · assigned to {{ $selected->assignedBackpackUser->name }} @endif
                        </div>
                    </div>
                    <div class="flex gap-1 flex-wrap">
                        <flux:button size="xs" variant="ghost" wire:click="assignToMe" icon="user" class="rounded-xl">Assign</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('awaiting_customer')" icon="clock" class="rounded-xl">Awaiting</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('resolved')" icon="check-circle" class="rounded-xl">Resolve</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="setStatus('closed')" icon="x-circle" class="rounded-xl">Close</flux:button>
                    </div>
                </div>

                <div class="flux-chat-wallpaper flex-1 min-h-0 space-y-4 overflow-y-auto p-5">
                    @foreach($selected->messages->sortBy('id') as $m)
                        <div class="flex {{ $m->sender_type === 'staff' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $m->id }}">
                            <div style="border-radius: 12px 12px {{ $m->sender_type === 'staff' ? '2px 12px' : '12px 2px' }};" class="flux-chat-bubble {{ $m->sender_type === 'staff' ? 'flux-chat-own bg-zinc-950 text-white dark:bg-white dark:text-zinc-950' : 'flux-chat-other border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white' }} max-w-[78%] overflow-hidden px-4 py-4 text-sm leading-5 shadow-md">
                                @if($m->deleted_at)
                                    <div class="mb-1 text-xs font-semibold text-amber-600">Deleted — {{ $m->deleted_by_role ?: 'unknown' }} at {{ $m->deleted_at->format('d M Y H:i') }}</div>
                                @endif
                                <div class="whitespace-pre-wrap">{{ $m->body }}</div>
                                @if($m->attachments->isNotEmpty())
                                    <div style="border-radius: 15px;" class="-mx-1 mt-3 space-y-2 rounded-2xl border p-1.5 {{ $m->sender_type === 'staff' ? 'border-white/20 bg-white/10' : 'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/70' }}">
                                        @foreach($m->attachments as $attachment)
                                            <a style="border-radius: 11px;" href="{{ route('support.attachments.show', $attachment) }}" class="flex min-w-0 items-center gap-3 rounded-xl border px-3 py-2.5 text-xs no-underline transition hover:opacity-80 {{ $m->sender_type === 'staff' ? 'border-white/20 bg-white/10' : 'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900' }}" target="_blank" rel="noopener">
                                                <span class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $m->sender_type === 'staff' ? 'bg-white/15' : 'bg-zinc-100 dark:bg-zinc-800' }}"><flux:icon name="paper-clip" class="size-4" /></span>
                                                <span class="min-w-0 truncate underline-offset-2 hover:underline">{{ $attachment->original_name }}</span>
                                                <flux:icon name="arrow-down-tray" class="ml-auto size-3.5 shrink-0 opacity-70" />
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-1 text-[10px] opacity-70">
                                    {{ $m->sender_type === 'staff' ? ($m->senderUser?->name ?: 'Staff') : 'Customer' }}
                                    · {{ $m->created_at?->format('d M H:i') }}
                                </div>
                                @if(! $m->deleted_at)
                                    <button type="button" wire:click="deleteMessage({{ $m->id }})" wire:confirm="Hide this message for everyone?" class="mt-1 text-[10px] underline opacity-80">Delete</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit.prevent="sendMessage" class="space-y-2 border-t border-zinc-200 p-4 dark:border-zinc-800" novalidate>
                    <div class="flex items-end gap-2">
                        <div class="min-w-0 flex-1">
                            <flux:textarea wire:model="newMessage" rows="1" class="min-h-12 rounded-2xl" placeholder="Type a reply…" />
                        </div>
                        <label class="flex h-12 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border border-zinc-300 bg-zinc-50 px-4 text-xs font-medium text-zinc-600 transition hover:border-zinc-950 hover:text-zinc-950 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:border-white dark:hover:text-white">
                            <flux:icon name="paper-clip" class="size-4" /><span class="hidden sm:inline">Attach</span>
                            <input type="file" wire:model="messageFiles" multiple class="sr-only" accept="image/jpeg,image/png,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,video/mp4,video/quicktime,video/webm">
                        </label>
                        <flux:button type="submit" variant="primary" icon="paper-airplane" class="!m-0 size-12 shrink-0 rounded-full p-0" aria-label="Send message"></flux:button>
                    </div>
                    @error('newMessage') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-zinc-500">Up to 5 files, 100MB each · images, documents, or video.</p>
                    @error('messageFiles') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
                    @error('messageFiles.*') <p class="text-sm text-brand-red">{{ $message }}</p> @enderror
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
