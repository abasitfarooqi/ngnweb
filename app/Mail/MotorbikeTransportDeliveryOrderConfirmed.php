<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MotorbikeTransportDeliveryOrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Motorbike delivery order confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: [
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.motorbike_transport_delivery_order_confirmed',
                    ['order' => $this->order],
                    ['title' => 'Motorbike delivery order confirmed'],
                ),
            ],
        );
    }
}
