<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_uuid' => $this->conversation?->uuid,
            'sender_type' => $this->sender_type,
            'sender_name' => $this->sender_type === 'staff'
                ? ($this->senderUser?->full_name ?? 'Staff')
                : ($this->senderCustomerAuth?->customer?->full_name ?? $this->senderCustomerAuth?->email ?? 'Customer'),
            'body' => (string) ($this->body ?? ''),
            'reply_to_message_id' => $this->reply_to_message_id,
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'deleted_by_user_id' => $this->deleted_by_user_id,
            'deleted_by_role' => $this->deleted_by_role,
            'reply_preview' => $this->whenLoaded('replyTo', fn () => $this->replyTo ? str((string) $this->replyTo->body)->limit(120)->toString() : null),
            'attachments' => $this->attachments->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->original_name,
                'mime' => $a->mime,
                'size' => $a->size,
                'api_url' => route('api.customer.support.attachments.show', $a->id),
                'api_url_customer' => route('api.customer.support.attachments.show', $a->id),
                'api_url_staff' => route('api.staff.support.attachments.show', $a->id),
                'url' => route('support.attachments.show', $a),
            ])->values(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'delivery_state' => 'delivered',
        ];
    }
}
