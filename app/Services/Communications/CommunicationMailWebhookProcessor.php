<?php

namespace App\Services\Communications;

use App\Models\CommunicationDelivery;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CommunicationMailWebhookProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): ?CommunicationDelivery
    {
        if (! app(CommunicationSchema::class)->ready()) {
            return null;
        }

        $normalized = $this->normalize($payload);
        $event = $normalized['event'];
        $uuid = $normalized['uuid'];
        $messageId = $normalized['message_id'];

        if ($event === '' || ($uuid === '' && $messageId === '')) {
            return null;
        }

        $delivery = $this->findEmailDelivery($uuid, $messageId);
        if ($delivery === null) {
            return null;
        }

        $this->applyEvent($delivery, $event, $normalized);

        return $delivery->fresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{event: string, uuid: string, message_id: string, provider: string}
     */
    private function normalize(array $payload): array
    {
        if (isset($payload['Message']) && is_string($payload['Message'])) {
            $decoded = json_decode($payload['Message'], true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $eventData = is_array($payload['event-data'] ?? null) ? $payload['event-data'] : [];
        $mail = is_array($payload['mail'] ?? null) ? $payload['mail'] : [];
        $headers = $this->headerMap($payload, $eventData, $mail);

        $event = strtolower(trim((string) (
            $payload['event']
            ?? $eventData['event']
            ?? $payload['notificationType']
            ?? $payload['eventType']
            ?? ''
        )));

        $uuid = trim((string) (
            $payload['uuid']
            ?? $payload['communication_uuid']
            ?? data_get($eventData, 'user-variables.communication_uuid')
            ?? data_get($eventData, 'user-variables.X-NGN-Communication-UUID')
            ?? $headers['x-ngn-communication-uuid']
            ?? ''
        ));

        $messageId = trim((string) (
            $payload['message_id']
            ?? $payload['Message-Id']
            ?? $payload['Message-ID']
            ?? data_get($eventData, 'message.headers.message-id')
            ?? $mail['messageId']
            ?? $headers['message-id']
            ?? ''
        ), " \t\n\r\0\x0B<>");

        $provider = 'webhook';
        if ($eventData !== []) {
            $provider = 'mailgun';
        } elseif (isset($payload['notificationType']) || isset($payload['eventType']) || isset($mail['messageId'])) {
            $provider = 'ses';
        }

        return [
            'event' => $event,
            'uuid' => $uuid,
            'message_id' => $messageId,
            'provider' => $provider,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $eventData
     * @param  array<string, mixed>  $mail
     * @return array<string, string>
     */
    private function headerMap(array $payload, array $eventData, array $mail): array
    {
        $map = [];

        foreach ([$payload['headers'] ?? null, $mail['headers'] ?? null, data_get($eventData, 'message.headers')] as $headers) {
            if (! is_array($headers)) {
                continue;
            }

            if (isset($headers['message-id']) || isset($headers['Message-Id'])) {
                foreach ($headers as $key => $value) {
                    if (is_string($key) && (is_string($value) || is_numeric($value))) {
                        $map[strtolower($key)] = (string) $value;
                    }
                }

                continue;
            }

            foreach ($headers as $header) {
                if (! is_array($header)) {
                    continue;
                }

                $name = strtolower(trim((string) ($header['name'] ?? $header['Name'] ?? '')));
                $value = (string) ($header['value'] ?? $header['Value'] ?? '');
                if ($name !== '') {
                    $map[$name] = $value;
                }
            }
        }

        return $map;
    }

    private function findEmailDelivery(string $uuid, string $messageId): ?CommunicationDelivery
    {
        $query = CommunicationDelivery::query()->where('channel', 'email');

        if ($uuid !== '') {
            $query->whereHas('communication', fn ($inner) => $inner->where('uuid', $uuid));
        } elseif ($messageId !== '') {
            $normalized = Str::lower($messageId);
            $query->where(function ($inner) use ($messageId, $normalized): void {
                $inner->where('provider_message_id', $messageId)
                    ->orWhereRaw('LOWER(provider_message_id) = ?', [$normalized]);
            });
        }

        return $query->latest('id')->first();
    }

    /**
     * @param  array{event: string, uuid: string, message_id: string, provider: string}  $normalized
     */
    private function applyEvent(CommunicationDelivery $delivery, string $event, array $normalized): void
    {
        $now = Carbon::now();
        $metadata = is_array($delivery->metadata) ? $delivery->metadata : [];
        $metadata['provider_event'] = $event;
        $metadata['provider_event_at'] = $now->toIso8601String();

        if ($normalized['message_id'] !== '' && ! $delivery->provider_message_id) {
            $delivery->provider_message_id = $normalized['message_id'];
        }

        $status = $delivery->status;
        $deliveredAt = $delivery->delivered_at;
        $failedAt = $delivery->failed_at;
        $failureReason = $delivery->failure_reason;

        if (in_array($event, ['delivered', 'delivery'], true)) {
            $status = 'delivered';
            $deliveredAt = $deliveredAt ?: $now;
            $failedAt = null;
            $failureReason = null;
        } elseif (in_array($event, ['opened', 'open'], true)) {
            $metadata['opened_at'] = $now->toIso8601String();
            if (! in_array($status, ['failed', 'bounced'], true)) {
                $status = $status === 'sent' || $status === 'pending' ? 'delivered' : $status;
                $deliveredAt = $deliveredAt ?: $now;
            }
        } elseif (in_array($event, ['bounced', 'bounce', 'permanent_fail'], true)) {
            $status = 'bounced';
            $failedAt = $now;
            $failureReason = 'Provider reported bounce.';
        } elseif (in_array($event, ['failed', 'failure', 'rejected', 'complained', 'complaint'], true)) {
            $status = 'failed';
            $failedAt = $now;
            $failureReason = 'Provider reported '.$event.'.';
        } else {
            return;
        }

        $delivery->forceFill([
            'status' => $status,
            'provider' => $delivery->provider ?: $normalized['provider'],
            'delivered_at' => $deliveredAt,
            'failed_at' => $failedAt,
            'failure_reason' => $failureReason,
            'metadata' => $metadata,
        ])->save();
    }
}
