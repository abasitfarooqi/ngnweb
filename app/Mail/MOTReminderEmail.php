<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MOTReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MOT Reminder Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'olders.emails.mot_notifier_30_and_10_days',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->view('olders.emails.mot_notifier_30_and_10_days')
            ->subject('MOT Reminder Notification')
            ->with([
                'customer_name' => $this->emailData['customer_name'],
                'mot_due_date' => $this->emailData['mot_due_date'],
                'tax_due_date' => $this->emailData['tax_due_date'],
                'insurance_due_date' => $this->emailData['insurance_due_date'],
                'motorbike_reg' => $this->emailData['motorbike_reg'],
                'motorbike_model' => $this->emailData['motorbike_model'],
                'motorbike_make' => $this->emailData['motorbike_make'],
                'motorbike_year' => $this->emailData['motorbike_year'],
                'motorbike_id' => $this->emailData['motorbike_id'],
            ]);
    }
}
