<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HireContract extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hire Contract',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rental-agreement',
            with: $this->mailData

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Check if this is a battery safety leaflet email
        $isBatterySafetyLeaflet = isset($this->mailData['title']) && $this->mailData['title'] === 'E-Bike Battery Safety Leaflet';

        if (is_array($this->mailData['pdf']) && ! isset($this->mailData['pdf']->output)) {
            // Handle array of PDFs
            foreach ($this->mailData['pdf'] as $index => $pdf) {
                if ($pdf && method_exists($pdf, 'output')) {
                    $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'Sale-Agreement-'.($index + 1).'.pdf';
                    $attachments[] = Attachment::fromData(
                        fn () => $pdf->output(),
                        $filename
                    )->withMime('application/pdf');
                }
            }
        } else {
            // Handle single PDF
            if (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
                $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'Sale-Agreement.pdf';
                $attachments[] = Attachment::fromData(
                    fn () => $this->mailData['pdf']->output(),
                    $filename
                )->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
