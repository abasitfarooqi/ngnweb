<?php

namespace App\Notifications;

use App\Models\SupportConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportConversationStartedNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportConversation $conversation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = backpack_url('support-conversation/'.$this->conversation->id.'/show');

        return (new MailMessage)
            ->subject('New customer chat started')
            ->greeting('New support conversation')
            ->line('A customer started a new support chat that needs review.')
            ->line('Conversation: #'.$this->conversation->id)
            ->line('Topic: '.($this->conversation->topic ?: 'General enquiry'))
            ->action('Open conversation', $url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'conversation_uuid' => $this->conversation->uuid,
            'topic' => $this->conversation->topic,
            'status' => $this->conversation->status,
        ];
    }
}
