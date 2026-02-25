<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitWeeklyOpeningReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $summary,
        public array $expectedItems,
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
            ->subject("Weekly MIT Collection Schedule - Week of {$this->weekStart}")
            ->markdown('emails.mit-weekly-opening-report', [
                'summary' => $this->summary,
                'expectedItems' => $this->expectedItems,
                'weekStart' => $this->weekStart,
                'weekEnd' => $this->weekEnd,
                'userName' => $notifiable->name,
            ]);
    }
}
