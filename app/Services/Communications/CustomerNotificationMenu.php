<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CustomerNotificationMenu
{
    /**
     * @return array{unread: int, items: Collection<int, Communication>}
     */
    public function forCurrentCustomer(): array
    {
        return once(function (): array {
            $empty = ['unread' => 0, 'items' => collect()];
            $customer = Auth::guard('customer')->user();
            if (! $customer || ! app(CommunicationSchema::class)->ready()) {
                return $empty;
            }

            try {
                app(CommunicationInboxClaimer::class)->claimFor($customer);

                $unread = CommunicationRecipient::query()
                    ->where('customer_auth_id', $customer->id)
                    ->whereNull('read_at')
                    ->whereNull('archived_at')
                    ->count();

                $items = Communication::query()
                    ->with(['recipients' => fn ($q) => $q->where('customer_auth_id', $customer->id)])
                    ->whereHas(
                        'recipients',
                        fn ($q) => $q->where('customer_auth_id', $customer->id)->whereNull('archived_at')
                    )
                    ->latest()
                    ->limit(5)
                    ->get();

                return ['unread' => $unread, 'items' => $items];
            } catch (Throwable) {
                return $empty;
            }
        });
    }

    /**
     * @return array{unread: int, items: list<array{uuid: string, title: string, preview: string, created_at: string, unread: bool}>}
     */
    public function livePayload(): array
    {
        $menu = $this->forCurrentCustomer();

        return [
            'unread' => (int) ($menu['unread'] ?? 0),
            'items' => ($menu['items'] ?? collect())->map(function ($communication): array {
                $recipient = $communication->recipients->first();

                return [
                    'uuid' => (string) $communication->uuid,
                    'title' => (string) $communication->title,
                    'preview' => (string) $communication->preview,
                    'created_at' => (string) $communication->created_at?->format('d M Y H:i'),
                    'unread' => $recipient?->read_at === null,
                ];
            })->values()->all(),
        ];
    }
}
