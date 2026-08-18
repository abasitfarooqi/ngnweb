<?php

namespace App\Services\Communications;

use App\Events\CustomerCommunicationCreated;
use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\CustomerDeviceToken;
use App\Services\ExpoPushNotificationService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommunicationPushDispatcher
{
    public function dispatch(Communication $communication, CommunicationOutboundPlan $plan): void
    {
        $policy = $communication->policy_snapshot ?? [];
        $webPush = (bool) data_get($policy, 'web_push_enabled', false);
        $mobilePush = (bool) data_get($policy, 'mobile_push_enabled', false);
        $customerAuth = $plan->customerAuth;

        $notifyCustomer = $customerAuth !== null && ($plan->decision->createInbox || $webPush || $mobilePush);

        if ($customerAuth === null) {
            $this->markDeferredOrSkipped($communication, 'web_push', $webPush, 'No customer portal account matched this email address.');
            $this->markDeferredOrSkipped($communication, 'mobile_push', $mobilePush, 'No customer portal account matched this email address.');
        }

        $this->broadcastAndMarkWebPush(
            $communication,
            $notifyCustomer ? $customerAuth->id : null,
            $webPush && $notifyCustomer,
        );

        if ($mobilePush && $customerAuth !== null) {
            $this->sendExpo($communication, $customerAuth->id);
        }
    }

    private function broadcastAndMarkWebPush(Communication $communication, ?int $customerAuthId, bool $webPush): void
    {
        $now = now();

        try {
            event(new CustomerCommunicationCreated($communication, $customerAuthId, $webPush));

            if ($webPush && $customerAuthId) {
                $this->upsertDelivery($communication, 'web_push', [
                    'status' => 'sent',
                    'provider' => 'pusher',
                    'queued_at' => $now,
                    'sent_at' => $now,
                    'failed_at' => null,
                    'failure_reason' => null,
                    'metadata' => ['channel' => 'communications.customer.'.$customerAuthId],
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Transactional communication realtime broadcast failed.', [
                'communication_id' => $communication->id,
                'message' => $exception->getMessage(),
            ]);

            if ($webPush) {
                $this->upsertDelivery($communication, 'web_push', [
                    'status' => 'failed',
                    'provider' => 'pusher',
                    'queued_at' => $now,
                    'failed_at' => $now,
                    'failure_reason' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function sendExpo(Communication $communication, int $customerAuthId): void
    {
        $now = now();
        $hasToken = CustomerDeviceToken::query()
            ->where('customer_auth_id', $customerAuthId)
            ->where('is_active', true)
            ->exists();

        if (! $hasToken) {
            $this->upsertDelivery($communication, 'mobile_push', [
                'status' => 'skipped',
                'provider' => 'expo',
                'queued_at' => $now,
                'failure_reason' => 'No Expo device tokens registered for this portal account.',
            ]);

            return;
        }

        try {
            app(ExpoPushNotificationService::class)->sendToCustomer(
                $customerAuthId,
                (string) $communication->title,
                (string) ($communication->preview ?: $communication->subject ?: $communication->title),
                [
                    'type' => 'communication',
                    'uuid' => $communication->uuid,
                ],
            );

            $this->upsertDelivery($communication, 'mobile_push', [
                'status' => 'sent',
                'provider' => 'expo',
                'queued_at' => $now,
                'sent_at' => $now,
                'failed_at' => null,
                'failure_reason' => null,
            ]);
        } catch (Throwable $exception) {
            $this->upsertDelivery($communication, 'mobile_push', [
                'status' => 'failed',
                'provider' => 'expo',
                'queued_at' => $now,
                'failed_at' => $now,
                'failure_reason' => $exception->getMessage(),
            ]);
        }
    }

    private function markDeferredOrSkipped(Communication $communication, string $channel, bool $enabled, string $reason): void
    {
        if (! $enabled) {
            return;
        }

        $now = now();

        $this->upsertDelivery($communication, $channel, [
            'status' => 'deferred',
            'queued_at' => $now,
            'failed_at' => null,
            'failure_reason' => $reason,
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function upsertDelivery(Communication $communication, string $channel, array $values): void
    {
        $existing = $communication->deliveries()
            ->where('channel', $channel)
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->forceFill($values)->save();

            return;
        }

        CommunicationDelivery::query()->create(array_merge([
            'communication_id' => $communication->id,
            'channel' => $channel,
        ], $values));
    }
}
