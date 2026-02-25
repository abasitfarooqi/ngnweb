<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NgnSurveySystemCampaignMailer extends Mailable
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
            subject: 'Participate in our Motorbike Preference Survey',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ngn_survey_system_campaign',
        );
    }

    public function build()
    {
        return $this->view('emails.ngn_survey_system_campaign')
            ->subject('Participate in our Motorbike Preference Survey')
            ->with([
                'name' => $this->emailData['name'],
                'surveyLink' => $this->emailData['surveyLink'],
                'email' => $this->emailData['email'],
                'phone' => $this->emailData['phone'],
                'ngn_survey_id' => $this->emailData['ngn_survey_id'],
            ]);
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
}
