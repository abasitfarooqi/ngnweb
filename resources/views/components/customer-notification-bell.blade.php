@auth('customer')
    @php
        $menu = app(\App\Services\Communications\CustomerNotificationMenu::class)->forCurrentCustomer();
        $unread = (int) ($menu['unread'] ?? 0);
        $items = $menu['items'] ?? collect();
    @endphp

    <div {{ $attributes->class('relative') }} x-data="{ open: false }" @keydown.escape.window="open = false">
        <button
            type="button"
            class="relative p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            @click="open = !open"
            :aria-expanded="open.toString()"
            aria-label="Notifications"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span
                class="js-notifications-unread absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 px-1 inline-flex items-center justify-center bg-brand-red text-white text-[10px] leading-none {{ $unread > 0 ? '' : 'hidden' }}"
                data-count="{{ $unread }}"
            >{{ $unread }}</span>
        </button>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            class="absolute right-0 top-full mt-2 z-50 w-80 max-w-[calc(100vw-2rem)] border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="border-b border-gray-200 px-3 py-2 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
            </div>
            <div class="js-notifications-dropdown-list max-h-80 overflow-y-auto">
                @forelse($items as $communication)
                    @php($recipient = $communication->recipients->first())
                    <a
                        href="{{ route('account.notifications.show', $communication->uuid) }}"
                        class="block border-b border-gray-100 px-3 py-2.5 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/60"
                        data-notification-uuid="{{ $communication->uuid }}"
                    >
                        <p class="flex items-start justify-between gap-2 text-sm font-medium text-gray-900 dark:text-white">
                            <span class="min-w-0 truncate">{{ $communication->title }}</span>
                            @if($recipient?->read_at === null)
                                <span class="mt-1 inline-block h-2 w-2 shrink-0 bg-brand-red"></span>
                            @endif
                        </p>
                        @if($communication->preview)
                            <p class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $communication->preview }}</p>
                        @endif
                        <p class="mt-1 text-[11px] text-gray-400">{{ $communication->created_at?->format('d M Y H:i') }}</p>
                    </a>
                @empty
                    <p class="js-notifications-empty px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No notifications yet.</p>
                @endforelse
            </div>
            <a
                href="{{ route('account.notifications') }}"
                class="block border-t border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-sm font-semibold text-brand-red hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-700"
            >
                View all notifications
            </a>
        </div>
    </div>
@endauth
