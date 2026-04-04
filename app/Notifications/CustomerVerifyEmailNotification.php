<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CustomerVerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        Log::info('CustomerVerifyEmailNotification constructed');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        Log::info('CustomerVerifyEmailNotification via method called');

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        Log::info('Verification URL generated', ['url' => $verificationUrl]);
        $customer = $notifiable->customer;
        if (! $customer) {
            $customer = (object) [
                'first_name' => 'Customer',
                'last_name' => '',
                'email' => $notifiable->email ?? '',
            ];
        }

        try {
            return (new MailMessage)
                ->subject('Verify your email address - NGN Motors')
                ->view('emails.templates.agreement-controller-universal', [
                    'mailData' => [
                        'title' => 'Verify your email address',
                        'subject' => 'Verify your email address - NGN Motors',
                        'greetingName' => trim((string) ($customer->first_name ?? 'there')),
                        'introLines' => [
                            'Thank you for creating your NGN Motors account.',
                            'Please confirm your email address to activate your account and continue using all portal and store features.',
                        ],
                        'url' => $verificationUrl,
                        'actionLabel' => 'Verify email address',
                        'details' => [
                            'Account Email' => (string) ($customer->email ?? ''),
                            'Verification Link Expiry' => '60 minutes',
                        ],
                        'outroLines' => [
                            'If you did not create this account, you can safely ignore this email.',
                        ],
                    ],
                ]);
        } catch (\Exception $e) {
            Log::error('Error in toMail method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'customer.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
