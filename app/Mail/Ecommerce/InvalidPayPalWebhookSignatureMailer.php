<?php

// app/Mail/Ecommerce/InvalidPayPalWebhookSignatureMailer.php

namespace App\Mail\Ecommerce;

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
        return $this->view('emails.ecommerce.invalid-paypal-webhook-signature')
            ->subject('Invalid PayPal Webhook Signature Notification')
            ->with([
                'eventType' => $this->eventType,
                'resource' => $this->resource,
                'payment' => $this->payment,
                'webhookEvent' => $this->webhookEvent,
            ]);
    }
}
