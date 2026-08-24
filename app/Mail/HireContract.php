<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HireContract extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;

    protected $mailData;

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
            subject: $this->mailData['title'] ?? 'Sale Contract',
            cc: array_values(array_filter((array) ($this->mailData['cc'] ?? []))),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: ['mailData' => $this->mailData],
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
                        : 'Sale-Agreement-'.($index + 1).'.pdf';
                }

                $attachments[] = Attachment::fromPath($path)
                    ->as($filename)
                    ->withMime('application/pdf');
            }

            if ($attachments !== []) {
                return $attachments;
            }
        }

        $pdf = $this->mailData['pdf'] ?? null;
        if ($pdf === null) {
            return $attachments;
        }

        if (is_array($pdf)) {
            foreach ($pdf as $index => $item) {
                if ($item && method_exists($item, 'output')) {
                    $filename = $isBatterySafetyLeaflet
                        ? 'batterySafetyDataLeaflet.pdf'
                        : 'Sale-Agreement-'.($index + 1).'.pdf';
                    $attachments[] = Attachment::fromData(
                        fn () => $item->output(),
                        $filename
                    )->withMime('application/pdf');
                }
            }

            return $attachments;
        }

        if (method_exists($pdf, 'output')) {
            $filename = $isBatterySafetyLeaflet ? 'batterySafetyDataLeaflet.pdf' : 'Sale-Agreement.pdf';
            $attachments[] = Attachment::fromData(
                fn () => $pdf->output(),
                $filename
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
