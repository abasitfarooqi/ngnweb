<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'service_booking_id' => $this->service_booking_id,
            'title' => $this->title,
            'topic' => $this->topic,
            'status' => $this->status,
            'customer_auth_id' => $this->customer_auth_id,
            'customer_name' => $this->customerAuth?->customer?->full_name ?? $this->customerAuth?->email,
            'customer_email' => $this->customerAuth?->email,
            'assigned_staff_name' => $this->assignedBackpackUser?->full_name,
            'assigned_backpack_user_id' => $this->assigned_backpack_user_id,
            'last_message_at' => optional($this->last_message_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'latest_message' => $this->whenLoaded('messages', function () {
                $latest = $this->messages->sortByDesc('id')->first();

                return $latest ? (new SupportMessageResource($latest))->toArray(request()) : null;
            }),
        ];
    }
}
