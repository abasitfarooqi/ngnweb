<?php

namespace App\Events;

use App\Models\Communication;
use App\Models\CommunicationReply;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCommunicationReplyCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Communication $communication,
        public CommunicationReply $reply,
        public int $customerAuthId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('communications.staff'),
            new PrivateChannel('communications.customer.'.$this->customerAuthId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'communication.reply';
    }

    public function broadcastWith(): array
    {
        return [
            'uuid' => $this->communication->uuid,
            'reply_id' => $this->reply->id,
            'author_type' => $this->reply->author_type,
            'body' => $this->reply->body,
            'created_at' => optional($this->reply->created_at)->toIso8601String(),
        ];
    }
}
