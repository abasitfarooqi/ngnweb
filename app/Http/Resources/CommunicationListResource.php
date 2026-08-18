<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recipient = $this->recipients->first();

        return [
            'uuid' => $this->uuid,
            'communication_key' => $this->communication_key,
            'title' => $this->title,
            'subject' => $this->subject,
            'preview' => $this->preview,
            'priority' => $this->priority,
            'category' => $this->category,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'seen' => $recipient?->seen_at !== null,
            'read' => $recipient?->read_at !== null,
            'archived' => $recipient?->archived_at !== null,
            'has_attachments' => (int) $this->attachments_count > 0,
            'attachment_count' => (int) $this->attachments_count,
            'reply_allowed' => (bool) data_get($this->policy_snapshot, 'reply_allowed', false),
            'enquiry_allowed' => (bool) data_get($this->policy_snapshot, 'enquiry_allowed', false),
        ];
    }
}
