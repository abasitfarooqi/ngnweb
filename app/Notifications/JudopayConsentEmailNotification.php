<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JudopayConsentEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $authorizationUrl;

    protected string $customerName;

    protected string $serviceType;

    protected string $expiresAt;

    protected ?string $subscriptionId = null;

    protected ?string $contractId = null;

    protected ?string $vrm = null;

    public function __construct(string $authorizationUrl, string $customerName, string $serviceType = '', string $expiresAt = '', ?string $subscriptionId = null, ?string $contractId = null, ?string $vrm = null)
    {
        $this->authorizationUrl = $authorizationUrl;
        $this->customerName = $customerName;
        $this->serviceType = $serviceType;
        $this->expiresAt = $expiresAt;
        $this->subscriptionId = $subscriptionId;
        $this->contractId = $contractId;
        $this->vrm = $vrm;
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
        $serviceInfo = $this->serviceType ? " for your {$this->serviceType} service" : '';
        $expiryInfo = $this->expiresAt ? " This link expires on {$this->expiresAt}." : '';

        $lines = [];
        if ($this->subscriptionId) {
            $lines[] = "Subscription ID: {$this->subscriptionId}";
        }
        if ($this->contractId) {
            $lines[] = "Contract ID: {$this->contractId}";
        }
        if ($this->vrm) {
            $lines[] = "VRM: {$this->vrm}";
        }
        $summary = empty($lines) ? null : implode(' • ', $lines);

        return (new MailMessage)
            ->subject('Authorise Recurring Payments - Neguinho Motors')
            ->greeting("Hello {$this->customerName}!")
            ->line('You are setting up recurring payments with Neguinho Motors.')
            ->line($summary ?: 'This authorisation relates to your subscription with us.')
            ->line('To complete your authorisation and set up secure recurring payments, please click the button below:')
            ->action('Authorise Recurring Payments', $this->authorizationUrl)
            ->line('🔒 This is a secure authorisation link that will allow you to set up recurring payments'.$serviceInfo.'.')
            ->line('You will receive your payment schedule shortly, or you can contact us to confirm it.')
            ->line($expiryInfo)
            ->line('If you did not request this authorisation, please ignore this email.')
            ->line('Security notice: we never send links to set up payments via SMS, WhatsApp or phone. Only trust messages from this email address.')
            ->line('Thank you for choosing Neguinho Motors!')
            ->salutation('Regards, The Neguinho Motors Team');
    }
}
