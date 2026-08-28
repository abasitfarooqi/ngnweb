<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationResource extends JsonResource
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
            'content' => [
                'html' => $this->content_html,
                'text' => $this->content_text,
                'structured' => $this->structured_content,
            ],
            'source' => [
                'type' => $this->source_type,
                'id' => $this->source_id,
                'correlation_id' => $this->correlation_id,
            ],
            'template_version' => $this->template_version,
            'read_state' => [
                'seen_at' => optional($recipient?->seen_at)->toIso8601String(),
                'read_at' => optional($recipient?->read_at)->toIso8601String(),
                'archived_at' => optional($recipient?->archived_at)->toIso8601String(),
            ],
            'reply_allowed' => true,
            'enquiry_allowed' => (bool) data_get($this->policy_snapshot, 'enquiry_allowed', false),
            'attachments' => CommunicationAttachmentResource::collection($this->whenLoaded('attachments')),
            'replies' => $this->whenLoaded('replies', fn () => $this->replies->map(fn ($reply) => [
                'id' => $reply->id,
                'author_type' => $reply->author_type,
                'author_label' => $reply->authorLabel(),
                'body' => $reply->body,
                'created_at' => optional($reply->created_at)->toIso8601String(),
            ])->values()->all()),
        ];
    }
}
