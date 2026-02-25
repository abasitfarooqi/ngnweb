<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class RentalAgreementNgn extends Mailable
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
            view: 'emails.rental-agreement',
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

        // Send pdf1, pdf2, pdf3 if available. If not, send pdf only
        if (
            isset($this->mailData['pdf1']) && method_exists($this->mailData['pdf1'], 'output') &&
            isset($this->mailData['pdf2']) && method_exists($this->mailData['pdf2'], 'output') &&
            isset($this->mailData['pdf3']) && method_exists($this->mailData['pdf3'], 'output')
        ) {

            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf1']->output(),
                'Rental-Agreement-NGN-1.pdf'
            )->withMime('application/pdf');

            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf2']->output(),
                'Rental-Agreement-NGN-2.pdf'
            )->withMime('application/pdf');

            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf3']->output(),
                'Rental-Agreement-NGN-3.pdf'
            )->withMime('application/pdf');

        } elseif (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
            // Fallback to single pdf
            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf']->output(),
                'Rental-Agreement-NGN.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
