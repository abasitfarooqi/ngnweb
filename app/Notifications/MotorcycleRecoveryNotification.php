<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MotorcycleRecoveryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $distance;

    protected $fromAddress;

    protected $toAddress;

    protected $userDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(float $distance, string $fromAddress, string $toAddress, array $userDetails)
    {
        $this->distance = $distance;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->userDetails = $userDetails; // Store user details
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Send via email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Motorcycle Recovery Order Confirmation')
            ->greeting('Hello '.$this->userDetails['name'].',')
            ->line('Your motorcycle recovery order has been successfully submitted.')
            ->line('From Address: '.$this->fromAddress)
            ->line('To Address: '.$this->toAddress)
            ->line('Total Distance: '.$this->distance.' miles')
            ->line('Your Details:')
            ->line('Name: '.$this->userDetails['name'])
            ->line('Email: '.$this->userDetails['email'])
            ->line('Phone: '.$this->userDetails['phone'])
            ->line('Thank you for choosing our service!')
            ->action('View Order', url('/motorbike-recovery/completed')); // Adjust the URL as needed
    }
}
