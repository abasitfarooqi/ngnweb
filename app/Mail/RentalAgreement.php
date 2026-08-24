<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalAgreement extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    protected $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Rental Agreement',
            cc: array_values(array_filter((array) ($this->mailData['cc'] ?? []))),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: ['mailData' => $this->mailData],
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

        $isBatterySafetyLeaflet = isset($this->mailData['title'])
            && $this->mailData['title'] === 'E-Bike Battery Safety Leaflet';

        if (! empty($this->mailData['pdf_files']) && is_array($this->mailData['pdf_files'])) {
            foreach ($this->mailData['pdf_files'] as $index => $file) {
                if (! is_array($file)) {
                    continue;
                }

                $path = (string) ($file['path'] ?? '');
                if ($path === '' || ! is_file($path) || (int) filesize($path) < 512) {
                    continue;
                }

                $filename = (string) ($file['name'] ?? '');
                if ($filename === '') {
                    $filename = $isBatterySafetyLeaflet
                        ? 'batterySafetyDataLeaflet.pdf'
                        : 'rental-agreement-'.($index + 1).'.pdf';
                }

                $attachments[] = Attachment::fromPath($path)
                    ->as($filename)
                    ->withMime('application/pdf');
            }

            if ($attachments !== []) {
                return $attachments;
            }
        }

        if (isset($this->mailData['pdf']) && is_array($this->mailData['pdf']) && ! isset($this->mailData['pdf']->output)) {
            // Handle array of PDFs
            foreach ($this->mailData['pdf'] as $index => $pdf) {
                if ($pdf && method_exists($pdf, 'output')) {
                    $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'rental-agreement-'.($index + 1).'.pdf';
                    $attachments[] = Attachment::fromData(
                        fn () => $pdf->output(),
                        $filename
                    )->withMime('application/pdf');
                }
            }
        } elseif (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
            // Handle single PDF
            $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'rental-agreement.pdf';
            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf']->output(),
                $filename
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
