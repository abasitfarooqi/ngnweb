<?php

// app/Mail/Ecommerce/InvalidPayPalWebhookSignatureMailer.php

namespace App\Mail\Ecommerce;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvalidPayPalWebhookSignatureMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $eventType;

    public $resource;

    public $payment;

    public $webhookEvent;

    public function __construct(string $eventType, array $resource, $payment, $webhookEvent)
    {
        $this->eventType = $eventType;
        $this->resource = $resource;
        $this->payment = $payment;
        $this->webhookEvent = $webhookEvent;
    }

    public function build()
    {
        return $this->subject('Invalid PayPal Webhook Signature Notification')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.invalid-paypal-webhook-signature',
                [
                    'eventType' => $this->eventType,
                    'resource' => $this->resource,
                    'payment' => $this->payment,
                    'webhookEvent' => $this->webhookEvent,
                ],
                'Invalid PayPal Webhook Signature Notification',
            ));
    }
}
