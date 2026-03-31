<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalAgreement extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Rental Agreement',
        );
    }

    public function content()
    {
        return new Content(
            view: 'olders.emails.rental-agreement',
            with: $this->mailData
        );
    }

    // public function attachments()
    // {
    //     return [
    //         Attachment::fromData(fn () => $this->mailData['pdf']->output(), 'Rental-Agreement.pdf')
    //             ->withMime('application/pdf'),
    //     ];
    // }

    public function attachments(): array
    {
        $attachments = [];

        // Check if this is a battery safety leaflet email
        $isBatterySafetyLeaflet = isset($this->mailData['title']) && $this->mailData['title'] === 'E-Bike Battery Safety Leaflet';

        if (is_array($this->mailData['pdf']) && ! isset($this->mailData['pdf']->output)) {
            // Handle array of PDFs
            foreach ($this->mailData['pdf'] as $index => $pdf) {
                if ($pdf && method_exists($pdf, 'output')) {
                    $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'Rental-Agreement-'.($index + 1).'.pdf';
                    $attachments[] = Attachment::fromData(
                        fn () => $pdf->output(),
                        $filename
                    )->withMime('application/pdf');
                }
            }
        } else {
            // Handle single PDF
            if (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
                $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'Rental-Agreement.pdf';
                $attachments[] = Attachment::fromData(
                    fn () => $this->mailData['pdf']->output(),
                    $filename
                )->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
