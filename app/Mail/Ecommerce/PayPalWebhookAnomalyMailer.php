<?php

namespace App\Mail\Ecommerce;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayPalWebhookAnomalyMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $anomalyType;

    public $eventType;

    public $resource;

    public $payment;

    public $webhookEvent;

    public $additionalData;

    public $errorMessage;

    public function __construct(
        string $anomalyType,
        ?string $eventType = null,
        ?array $resource = null,
        $payment = null,
        $webhookEvent = null,
        array $additionalData = [],
        ?string $errorMessage = null
    ) {
        $this->anomalyType = $anomalyType;
        $this->eventType = $eventType;
        $this->resource = $resource;
        $this->payment = $payment;
        $this->webhookEvent = $webhookEvent;
        $this->additionalData = $additionalData;
        $this->errorMessage = $errorMessage;
    }

    public function build()
    {
        $title = "PayPal Webhook Anomaly: {$this->anomalyType}";

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.ecommerce.paypal-webhook-anomaly',
                [
                    'anomalyType' => $this->anomalyType,
                    'eventType' => $this->eventType,
                    'resource' => $this->resource,
                    'payment' => $this->payment,
                    'webhookEvent' => $this->webhookEvent,
                    'additionalData' => $this->additionalData,
                    'errorMessage' => $this->errorMessage,
                ],
                $title,
            ));
    }
}
