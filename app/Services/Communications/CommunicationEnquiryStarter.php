<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CustomerAuth;
use App\Models\SupportConversation;

class CommunicationEnquiryStarter
{
    public function start(Communication $communication, CustomerAuth $customer): SupportConversation
    {
        abort_unless((bool) data_get($communication->policy_snapshot, 'enquiry_allowed', false), 403);

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
            'topic' => 'Communication enquiry',
            'status' => 'open',
        ]);

        $payload = is_array($communication->payload_snapshot) ? $communication->payload_snapshot : [];
        $payload['enquiry_conversation_uuid'] = $conversation->uuid;
        $communication->forceFill(['payload_snapshot' => $payload])->save();

        return $conversation;
    }
}
