<?php

namespace App\Listeners;

use App\Models\Communication;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Mail\Events\MessageSent;

class CaptureTransactionalMailMessageId
{
    public function handle(MessageSent $event): void
    {
        if (! app(CommunicationSchema::class)->ready()) {
            return;
        }

        $headers = $event->message->getHeaders();
        $uuidHeader = $headers->get('X-NGN-Communication-UUID');
        $uuid = $uuidHeader ? trim((string) $uuidHeader->getBodyAsString()) : '';

        if ($uuid === '') {
            return;
        }

        $messageIdHeader = $headers->get('Message-ID') ?: $headers->get('Message-Id');
        $messageId = $messageIdHeader ? trim((string) $messageIdHeader->getBodyAsString(), " \t\n\r\0\x0B<>") : '';

        $communication = Communication::query()->where('uuid', $uuid)->first();
        if ($communication === null) {
            return;
        }

        $delivery = $communication->deliveries()
            ->where('channel', 'email')
            ->latest('id')
            ->first();

        if ($delivery === null) {
            return;
        }

        $metadata = is_array($delivery->metadata) ? $delivery->metadata : [];
        $metadata['communication_uuid'] = $uuid;

        $values = ['metadata' => $metadata];
        if ($messageId !== '') {
            $values['provider_message_id'] = $messageId;
        }

        $delivery->forceFill($values)->save();
    }
}
