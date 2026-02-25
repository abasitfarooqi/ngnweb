<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitWeeklyClosingReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $summary,
        public array $detailedDeclines,
        public array $successItems,
        public string $weekStart,
        public string $weekEnd
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Weekly MIT Collection Summary - Week of {$this->weekStart}")
            ->markdown('emails.mit-weekly-closing-report', [
                'summary' => $this->summary,
                'detailedDeclines' => $this->detailedDeclines,
                'successItems' => $this->successItems,
                'weekStart' => $this->weekStart,
                'weekEnd' => $this->weekEnd,
                'userName' => $notifiable->name,
            ]);
    }
}
