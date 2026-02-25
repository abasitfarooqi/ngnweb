<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JudopayConsentSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $verificationCode;

    protected string $context;

    public function __construct(string $verificationCode, string $context = 'consent')
    {
        $this->verificationCode = $verificationCode;
        $this->context = $context;
    }

    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable)
    {
        $messages = [
            'consent' => "Your Neguinho Motors consent verification code is: {$this->verificationCode}. Valid for 10 minutes.",
            'payment' => "Your Neguinho Motors payment verification code is: {$this->verificationCode}. Valid for 10 minutes.",
        ];

        return $messages[$this->context] ?? $messages['consent'];
    }
}
