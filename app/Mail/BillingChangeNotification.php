<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Support\UniversalMailPayload;

class BillingChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Billing Settings Changed - Subscription #'.$this->data['subscription_id'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(

            view: 'emails.templates.agreement-controller-universal',

            with: [

                'mailData' => UniversalMailPayload::fromLegacyEmailView(

                    'livewire.agreements.migrated.emails.judopay.billing_change_notification',

                    is_array($this->data) ? $this->data : (array) $this->data,

                    ['title' => 'Billing Settings Changed - Subscription #'],

                ),

            ],

        );
    }

}
