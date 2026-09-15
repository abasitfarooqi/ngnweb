<div class="space-y-6" wire:poll.visible.5s="$refresh">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                <flux:icon name="paper-airplane" class="size-6" />
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:heading size="xl">Sent communications</flux:heading>
                    <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[11px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Live delivery log</span>
                </div>
                <flux:text class="mt-1">Track customer email, inbox delivery, reads, and staff visibility.</flux:text>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-sound-toggle class="js-enable-communication-alerts inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-800 shadow-sm transition hover:border-zinc-950 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:border-white">
                <flux:icon name="speaker-wave" class="size-4" />
                Sound on
            </button>
            <flux:button size="sm" variant="ghost" wire:click="markAllNotificationsRead" icon="check-circle" class="rounded-xl">Mark read</flux:button>
            @if($canManageCommunications)
                <a href="{{ route('flux-admin.communications.index') }}"><flux:button size="sm" variant="ghost" icon="arrow-left" class="rounded-xl">Control panel</flux:button></a>
            @endif
        </div>
    </div>
    <p class="js-communication-alerts-status -mt-4 pl-16 text-xs text-zinc-500 dark:text-zinc-400"></p>
    <div class="-mt-3 flex flex-wrap items-center gap-2 pl-16 text-xs text-zinc-500 dark:text-zinc-400">
        <span class="font-medium">Status:</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 font-medium text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300"><span class="size-1.5 rounded-full bg-emerald-500"></span>Read</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2 py-1 font-medium text-orange-700 dark:bg-orange-950/60 dark:text-orange-300"><span class="size-1.5 rounded-full bg-orange-500"></span>Unread</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 font-medium text-blue-700 dark:bg-blue-950/60 dark:text-blue-300"><span class="size-1.5 rounded-full bg-blue-500"></span>Delivered</span>
    </div>

    @if(! $schemaReady)
        <div class="border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">Communication tables are not migrated yet.</div>
    @else
        <x-flux-admin::data-table title="Message log" description="Email and portal delivery status for every communication.">
            <x-slot:toolbar>
                <x-flux-admin::filter-bar search-placeholder="Search communications…">
                    <div class="min-w-0 w-full">
                        <select wire:model.live="filters.type" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                            <option value="">Any type</option>
                            <option value="reminder">Reminders only</option>
                        </select>
                    </div>
                    <div class="min-w-0 w-full">
                        <select wire:model.live="filters.category" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                            <option value="">Any category</option>
                            @foreach($filterCategories as $category)
                                <option value="{{ $category }}">{{ ucfirst((string) $category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0 w-full">
                        <select wire:model.live="filters.email" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                            <option value="">Any email</option>
                            <option value="sent">Email sent</option>
                            <option value="failed">Email failed</option>
                            <option value="skipped">Email skipped</option>
                            <option value="none">No email row</option>
                        </select>
                    </div>
                    <div class="min-w-0 w-full">
                        <select wire:model.live="filters.inbox" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                            <option value="">Any inbox</option>
                            <option value="delivered">Inbox delivered</option>
                            <option value="deferred">Inbox waiting</option>
                            <option value="failed">Inbox failed</option>
                            <option value="off">Inbox off</option>
                        </select>
                    </div>
                    @if($hideReady)
                        <div class="min-w-0 w-full">
                            <select wire:model.live="filters.hidden" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                                <option value="">Visible</option>
                                <option value="hidden">Hidden from staff</option>
                                <option value="all">All, including hidden</option>
                            </select>
                        </div>
                    @endif
                </x-flux-admin::filter-bar>
            </x-slot:toolbar>

            <div class="grid gap-3 p-3 md:hidden" wire:key="sent-cards-{{ $realtimeTick }}">
                @forelse($rows as $row)
                    @php($email = $row->deliveries->firstWhere('channel', 'email'))
                    @php($inbox = $row->deliveries->firstWhere('channel', 'internal_inbox'))
                    @php($recipient = $row->recipients->first())
                    @php($staffRead = $staffReadReady ? $row->staffReads->firstWhere('user_id', auth()->id()) : null)
                    @php($isRead = $recipient?->read_at !== null)
                    <div class="rounded-2xl border border-zinc-200 border-l-4 {{ $isRead ? 'border-l-emerald-500 bg-white' : 'border-l-orange-500 bg-orange-50/40' }} p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 {{ $isRead ? 'dark:border-l-emerald-500' : 'dark:border-l-orange-500 dark:bg-orange-950/10' }}" wire:key="sent-card-{{ $row->id }}">
                        <div class="flex items-start gap-3"><span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="envelope" class="size-4" /></span><div class="min-w-0"><div class="font-semibold text-zinc-900 dark:text-white">{{ $row->title }}</div>
                        <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $row->communication_key }}</div>
                        </div></div>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $row->recipient_email ?: '—' }}</p>
                        <p class="mt-1 text-xs text-zinc-500">{{ $row->created_at?->format('d M Y H:i') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <flux:badge color="{{ ($email?->status ?? '') === 'sent' ? 'green' : (($email?->status ?? '') === 'failed' ? 'red' : (($email?->status ?? '') === 'skipped' ? 'orange' : 'zinc')) }}">Email {{ $email?->status ?? 'none' }}</flux:badge>
                            <flux:badge color="{{ ($inbox?->status ?? '') === 'delivered' ? 'blue' : (($inbox?->status ?? '') === 'failed' ? 'red' : (($inbox?->status ?? '') === 'deferred' ? 'orange' : 'zinc')) }}">Inbox {{ $inbox?->status ?? 'off' }}</flux:badge>
                            @if($row->isHiddenFromStaff())
                                <flux:badge color="zinc">Hidden</flux:badge>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-xs">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold {{ $isRead ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300' }}">
                                <span class="size-1.5 rounded-full {{ $isRead ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>{{ $isRead ? 'Read' : 'Unread' }}
                            </span>
                            <span class="text-zinc-500">{{ $recipient?->read_at?->format('d M Y H:i') ?? 'Not opened by customer' }}</span>
                        </div>
                        @if($staffReadReady)
                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            <input type="checkbox" class="size-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" @checked($staffRead?->dealt_at) wire:change="markAsDealt({{ $row->id }}, $event.target.checked)">
                            <span class="{{ $staffRead?->dealt_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-orange-700 dark:text-orange-300' }}">{{ $staffRead?->dealt_at ? 'Dealt' : 'Mark as dealt' }}</span>
                            @if($staffRead?->dealt_at)<span class="text-zinc-500">{{ $staffRead->dealt_at->format('d M Y H:i') }}</span>@endif
                        </label>
                        @endif
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('flux-admin.communications.sent.show', $row) }}">
                                <flux:button size="xs" variant="ghost" icon="eye" class="rounded-xl">View</flux:button>
                            </a>
                            @if($hideReady && ! $row->isHiddenFromStaff())
                                <flux:button size="xs" variant="ghost" icon="eye-slash" class="rounded-xl" wire:click="hideFromStaff({{ $row->id }})" wire:confirm="Hide this notification from staff? It stays in the log.">Hide</flux:button>
                            @elseif($hideReady)
                                <flux:button size="xs" variant="ghost" icon="eye" class="rounded-xl" wire:click="unhideFromStaff({{ $row->id } })">Show</flux:button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500">No notifications match these filters.</div>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-2xl border border-zinc-200 md:block dark:border-zinc-800">
                <flux:table wire:key="sent-log-{{ $realtimeTick }}">
                    <flux:table.columns>
                        <flux:table.column>Sent</flux:table.column>
                        <flux:table.column>Communication</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Email</flux:table.column>
                        <flux:table.column>Inbox</flux:table.column>
                        <flux:table.column>Read</flux:table.column>
                        @if($staffReadReady)<flux:table.column>Staff status</flux:table.column>@endif
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($rows as $row)
                            @php($email = $row->deliveries->firstWhere('channel', 'email'))
                            @php($inbox = $row->deliveries->firstWhere('channel', 'internal_inbox'))
                            @php($recipient = $row->recipients->first())
                            @php($staffRead = $staffReadReady ? $row->staffReads->firstWhere('user_id', auth()->id()) : null)
                            @php($isRead = $recipient?->read_at !== null)
                            <flux:table.row wire:key="sent-{{ $row->id }}" class="group {{ $isRead ? '' : 'bg-orange-50/50 dark:bg-orange-950/10' }}">
                                <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y H:i') }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2 font-medium text-zinc-900 dark:text-white"><span class="size-2 rounded-full {{ $isRead ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>{{ $row->title }}</div>
                                    <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $row->communication_key }}</div>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $row->recipient_email ?: '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ ($email?->status ?? '') === 'sent' ? 'green' : (($email?->status ?? '') === 'failed' ? 'red' : (($email?->status ?? '') === 'skipped' ? 'orange' : 'zinc')) }}">{{ $email?->status ?? 'none' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ ($inbox?->status ?? '') === 'delivered' ? 'blue' : (($inbox?->status ?? '') === 'failed' ? 'red' : (($inbox?->status ?? '') === 'deferred' ? 'orange' : 'zinc')) }}">{{ $inbox?->status ?? 'off' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $isRead ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300' }}">
                                        <span class="size-1.5 rounded-full {{ $isRead ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>{{ $isRead ? 'Read' : 'Unread' }}
                                    </span>
                                    <div class="mt-1 text-xs text-zinc-500">{{ $recipient?->read_at?->format('d M Y H:i') ?? 'Not opened' }}</div>
                                </flux:table.cell>
                                @if($staffReadReady)<flux:table.cell class="text-sm">
                                    <label class="flex items-center gap-2 text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        <input type="checkbox" class="size-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" @checked($staffRead?->dealt_at) wire:change="markAsDealt({{ $row->id }}, $event.target.checked)">
                                        <span class="{{ $staffRead?->dealt_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-orange-700 dark:text-orange-300' }}">{{ $staffRead?->dealt_at ? 'Dealt' : 'Mark as dealt' }}</span>
                                    </label>
                                    <div class="mt-1 text-xs text-zinc-500">
                                        {{ $staffRead?->opened_at?->format('d M Y H:i') ?? 'Not opened' }}
                                    </div>
                                </flux:table.cell>
                                @endif
                                <flux:table.cell>
                                    <div class="flex flex-wrap justify-end gap-1">
                                        <a href="{{ route('flux-admin.communications.sent.show', $row) }}">
                                            <flux:button size="xs" variant="ghost" icon="eye" class="rounded-xl">View</flux:button>
                                        </a>
                                        @if($hideReady && ! $row->isHiddenFromStaff())
                                            <flux:button size="xs" variant="ghost" icon="eye-slash" class="rounded-xl" wire:click="hideFromStaff({{ $row->id }})" wire:confirm="Hide this notification from staff? It stays in the log.">Hide</flux:button>
                                        @elseif($hideReady)
                                            <flux:button size="xs" variant="ghost" icon="eye" class="rounded-xl" wire:click="unhideFromStaff({{ $row->id }})">Show</flux:button>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="{{ $staffReadReady ? 8 : 7 }}" class="py-8 text-center text-sm text-zinc-500">No notifications match these filters.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <x-slot:footer>
                {{ $rows->links() }}
            </x-slot:footer>
        </x-flux-admin::data-table>
    @endif
</div>
