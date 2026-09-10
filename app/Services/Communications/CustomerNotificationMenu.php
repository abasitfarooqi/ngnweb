<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Models\SupportMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CustomerNotificationMenu
{
    /**
     * @return array{unread: int, chat_unread: int, header_unread: int, items: Collection<int, array<string, mixed>>}
     */
    public function forCurrentCustomer(): array
    {
        return once(function (): array {
            $empty = ['unread' => 0, 'chat_unread' => 0, 'header_unread' => 0, 'items' => collect()];
            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return $empty;
            }

            $emailUnread = 0;
            $emailItems = collect();
            try {
                if (app(CommunicationSchema::class)->ready()) {
                    app(CommunicationInboxClaimer::class)->claimFor($customer);

                    $emailUnread = CommunicationRecipient::query()
                        ->where('customer_auth_id', $customer->id)
                        ->whereNull('read_at')
                        ->whereNull('archived_at')
                        ->count();

                    $emailItems = Communication::query()
                        ->with(['recipients' => fn ($q) => $q->where('customer_auth_id', $customer->id)])
                        ->whereHas(
                            'recipients',
                            fn ($q) => $q->where('customer_auth_id', $customer->id)->whereNull('archived_at')
                        )
                        ->latest()
                        ->limit(5)
                        ->get()
                        ->map(function ($communication): array {
                            $recipient = $communication->recipients->first();

                            return [
                                'uuid' => (string) $communication->uuid,
                                'href' => route('account.notifications.show', $communication->uuid),
                                'title' => (string) $communication->title,
                                'preview' => (string) $communication->preview,
                                'created_at' => (string) $communication->created_at?->format('d M Y H:i'),
                                'sort_at' => (int) ($communication->created_at?->timestamp ?? 0),
                                'unread' => $recipient?->read_at === null,
                            ];
                        });
                }
            } catch (Throwable) {
                $emailUnread = 0;
                $emailItems = collect();
            }

            $chatUnread = 0;
            try {
                $chatUnread = SupportMessage::query()
                    ->where('sender_type', 'staff')
                    ->whereNull('read_at_customer')
                    ->whereNull('deleted_at')
                    ->whereHas('conversation', fn ($q) => $q->where('customer_auth_id', $customer->id))
                    ->count();
            } catch (Throwable) {
                $chatUnread = 0;
            }

            $chatNotice = collect();
            if ($chatUnread > 0) {
                $chatNotice = collect([[
                    'uuid' => 'chat-unread',
                    'href' => route('account.support.start-general'),
                    'title' => 'Unread text messages',
                    'preview' => 'You have one or more unread text messages.',
                    'created_at' => '',
                    'sort_at' => time(),
                    'unread' => true,
                ]]);
            }

            $items = $chatNotice
                ->concat($emailItems)
                ->sortByDesc('sort_at')
                ->take(6)
                ->values();

            return [
                'unread' => $emailUnread,
                'chat_unread' => $chatUnread,
                'header_unread' => $emailUnread + ($chatUnread > 0 ? 1 : 0),
                'items' => $items,
            ];
        });
    }

    /**
     * @return array{unread: int, chat_unread: int, header_unread: int, items: list<array{uuid: string, href: string, title: string, preview: string, created_at: string, unread: bool}>}
     */
    public function livePayload(): array
    {
        $menu = $this->forCurrentCustomer();

        return [
            'unread' => (int) ($menu['unread'] ?? 0),
            'chat_unread' => (int) ($menu['chat_unread'] ?? 0),
            'header_unread' => (int) ($menu['header_unread'] ?? 0),
            'items' => ($menu['items'] ?? collect())->map(function ($item): array {
                return [
                    'uuid' => (string) ($item['uuid'] ?? ''),
                    'href' => (string) ($item['href'] ?? ''),
                    'title' => (string) ($item['title'] ?? ''),
                    'preview' => (string) ($item['preview'] ?? ''),
                    'created_at' => (string) ($item['created_at'] ?? ''),
                    'unread' => (bool) ($item['unread'] ?? false),
                ];
            })->values()->all(),
        ];
    }
}
