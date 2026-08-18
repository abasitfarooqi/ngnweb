<?php

namespace App\Events;

use App\Models\Communication;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCommunicationCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Communication $communication,
        public ?int $customerAuthId,
        public bool $webPush = false,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('communications.staff'),
        ];

        if ($this->customerAuthId) {
            $channels[] = new PrivateChannel('communications.customer.'.$this->customerAuthId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'communication.created';
    }

    public function broadcastWith(): array
    {
        return [
            'uuid' => $this->communication->uuid,
            'title' => $this->communication->title,
            'subject' => $this->communication->subject,
            'preview' => $this->communication->preview,
            'web_push' => $this->webPush,
            'created_at' => optional($this->communication->created_at)->toIso8601String(),
        ];
    }
}
