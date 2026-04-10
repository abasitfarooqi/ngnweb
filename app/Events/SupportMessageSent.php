<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SupportMessage $message) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('support.conversation.'.$this->message->conversation->uuid),
        ];

        if ($this->message->conversation->customer_auth_id) {
            $channels[] = new PrivateChannel('support.customer.'.$this->message->conversation->customer_auth_id);
        }

        $channels[] = new PrivateChannel('support.staff');

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_uuid' => $this->message->conversation->uuid,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->message->sender_type === 'staff'
                ? ($this->message->senderUser?->full_name ?? 'Staff')
                : ($this->message->senderCustomerAuth?->customer?->full_name ?? $this->message->senderCustomerAuth?->email ?? 'Customer'),
            'body' => (string) ($this->message->body ?? ''),
            'attachments' => $this->message->attachments->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->original_name,
                'mime' => $a->mime,
                'size' => $a->size,
                'url' => route('support.attachments.show', $a),
            ])->values()->all(),
            'created_at' => optional($this->message->created_at)->toIso8601String(),
            'delivery_state' => 'delivered',
        ];
    }
}
