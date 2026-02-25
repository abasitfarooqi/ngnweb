<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MOTReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $motDetails;

    public function __construct($motDetails)
    {
        $this->motDetails = $motDetails;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return $this->buildMailMessage();
    }

    protected function buildMailMessage()
    {
        return (new MailMessage)
            ->subject('MOT Reminder Notification')
            ->greeting('Hello '.$this->motDetails['customer_name'].'!')
            ->line('Your MOT is due on '.$this->motDetails['mot_due_date']->format('Y-m-d').'.')
            ->line('Please book your MOT as soon as possible to avoid any last-minute hassles.')
            ->action('Book Now', url('/'))
            ->line('Thank you for using our application!');
    }
}
