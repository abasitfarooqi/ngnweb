<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MotorbikeTransportDeliveryOrderEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Motorbike transport delivery enquiry',
        );
    }

    public function content(): Content
    {
        $order = is_object($this->emailData)
            ? $this->emailData
            : (object) (array) $this->emailData;

        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: [
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.motorbike_transport_delivery_order_enquiry',
                    ['order' => $order],
                    ['title' => 'Motorbike transport delivery enquiry'],
                ),
            ],
        );
    }
}
