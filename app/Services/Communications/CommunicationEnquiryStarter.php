<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CustomerAuth;
use App\Models\SupportConversation;
use App\Models\SupportMessage;

class CommunicationEnquiryStarter
{
    public function start(Communication $communication, CustomerAuth $customer): SupportConversation
    {
        $existingUuid = data_get($communication->payload_snapshot, 'enquiry_conversation_uuid');
        if (is_string($existingUuid) && $existingUuid !== '') {
            $existing = SupportConversation::query()
                ->where('uuid', $existingUuid)
                ->where('customer_auth_id', $customer->id)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $conversation = SupportConversation::query()->create([
            'customer_auth_id' => $customer->id,
            'title' => 'Re: '.$communication->title,
            'topic' => 'Notification: '.$communication->title,
            'status' => 'open',
        ]);

        SupportMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_customer_auth_id' => $customer->id,
            'body' => "This chat is about the notification \"{$communication->title}\" sent on "
                .optional($communication->created_at)->format('d M Y H:i').'.',
            'meta' => [
                'communication_uuid' => $communication->uuid,
            ],
        ]);

        $payload = is_array($communication->payload_snapshot) ? $communication->payload_snapshot : [];
        $payload['enquiry_conversation_uuid'] = $conversation->uuid;
        $payload['enquiry_conversation_id'] = $conversation->id;
        $communication->forceFill(['payload_snapshot' => $payload])->save();

        return $conversation;
    }
}
