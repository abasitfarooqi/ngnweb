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
}
