<div class="space-y-6" wire:poll.1500ms="$refresh">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div class="min-w-0">
            <flux:heading size="xl">Notifications</flux:heading>
            <flux:text class="mt-1">Sent and received customer messages. Hide removes a row from this list only — the log is kept.</flux:text>
            <button type="button" data-sound-toggle class="js-enable-communication-alerts mt-3 inline-flex items-center gap-2 border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                Turn sound off
            </button>
            <p class="js-communication-alerts-status mt-1 text-xs text-zinc-500 dark:text-zinc-400"></p>
        </div>
        @if($canManageCommunications)
            <a href="{{ route('flux-admin.communications.index') }}">
                <flux:button size="sm" variant="ghost" icon="arrow-left" class="!rounded-none">Control panel</flux:button>
            </a>
        @endif
    </div>

    @if(! $schemaReady)
        <div class="border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">Communication tables are not migrated yet.</div>
    @else
        <x-flux-admin::data-table title="Message log" description="Email sent, skipped or failed, plus portal inbox delivery. Inbox off is off for the customer and for staff unless Staff copy was on for that send.">
            <x-slot:toolbar>
                <x-flux-admin::filter-bar search-placeholder="Search title, email or key...">
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

            <div class="divide-y divide-zinc-200 dark:divide-zinc-800 md:hidden" wire:key="sent-cards-{{ $realtimeTick }}">
                @forelse($rows as $row)
                    @php($email = $row->deliveries->firstWhere('channel', 'email'))
                    @php($inbox = $row->deliveries->firstWhere('channel', 'internal_inbox'))
                    @php($recipient = $row->recipients->first())
                    <div class="p-4" wire:key="sent-card-{{ $row->id }}">
                        <div class="font-medium text-zinc-900 dark:text-white">{{ $row->title }}</div>
                        <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $row->communication_key }}</div>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $row->recipient_email ?: '—' }}</p>
                        <p class="mt-1 text-xs text-zinc-500">{{ $row->created_at?->format('d M Y H:i') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <flux:badge color="{{ ($email?->status ?? '') === 'sent' ? 'green' : (($email?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">Email {{ $email?->status ?? 'none' }}</flux:badge>
                            <flux:badge color="{{ ($inbox?->status ?? '') === 'delivered' ? 'green' : (($inbox?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">Inbox {{ $inbox?->status ?? 'off' }}</flux:badge>
                            @if($row->isHiddenFromStaff())
                                <flux:badge color="zinc">Hidden</flux:badge>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">Read {{ $recipient?->read_at?->format('d M Y H:i') ?? '—' }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('flux-admin.communications.sent.show', $row) }}">
                                <flux:button size="xs" variant="ghost" class="!rounded-none">View</flux:button>
                            </a>
                            @if($hideReady && ! $row->isHiddenFromStaff())
                                <flux:button size="xs" variant="ghost" class="!rounded-none" wire:click="hideFromStaff({{ $row->id }})" wire:confirm="Hide this notification from staff? It stays in the log.">Hide</flux:button>
                            @elseif($hideReady)
                                <flux:button size="xs" variant="ghost" class="!rounded-none" wire:click="unhideFromStaff({{ $row->id }})">Show</flux:button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500">No notifications match these filters.</div>
                @endforelse
            </div>

            <div class="hidden md:block">
                <flux:table wire:key="sent-log-{{ $realtimeTick }}">
                    <flux:table.columns>
                        <flux:table.column>Sent</flux:table.column>
                        <flux:table.column>Communication</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Email</flux:table.column>
                        <flux:table.column>Inbox</flux:table.column>
                        <flux:table.column>Read</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($rows as $row)
                            @php($email = $row->deliveries->firstWhere('channel', 'email'))
                            @php($inbox = $row->deliveries->firstWhere('channel', 'internal_inbox'))
                            @php($recipient = $row->recipients->first())
                            <flux:table.row wire:key="sent-{{ $row->id }}">
                                <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">{{ $row->created_at?->format('d M Y H:i') }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $row->title }}</div>
                                    <div class="mt-1 font-mono text-[11px] text-zinc-500">{{ $row->communication_key }}</div>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $row->recipient_email ?: '—' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ ($email?->status ?? '') === 'sent' ? 'green' : (($email?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">{{ $email?->status ?? 'none' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ ($inbox?->status ?? '') === 'delivered' ? 'green' : (($inbox?->status ?? '') === 'failed' ? 'red' : 'zinc') }}">{{ $inbox?->status ?? 'off' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $recipient?->read_at?->format('d M Y H:i') ?? '—' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex flex-wrap justify-end gap-1">
                                        <a href="{{ route('flux-admin.communications.sent.show', $row) }}">
                                            <flux:button size="xs" variant="ghost" class="!rounded-none">View</flux:button>
                                        </a>
                                        @if($hideReady && ! $row->isHiddenFromStaff())
                                            <flux:button size="xs" variant="ghost" class="!rounded-none" wire:click="hideFromStaff({{ $row->id }})" wire:confirm="Hide this notification from staff? It stays in the log.">Hide</flux:button>
                                        @elseif($hideReady)
                                            <flux:button size="xs" variant="ghost" class="!rounded-none" wire:click="unhideFromStaff({{ $row->id }})">Show</flux:button>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="py-8 text-center text-sm text-zinc-500">No notifications match these filters.</flux:table.cell>
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
