<?php

namespace App\Mail;

use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoyaltySchemePolicy extends Mailable
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
            subject: 'Loyalty Upgrade Scheme Policy',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: ['mailData' => $this->mailData],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

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
                    $filename = 'Loyalty-Upgrade-Scheme-Policy'.($index > 0 ? '-'.($index + 1) : '').'.pdf';
                }

                $attachments[] = Attachment::fromPath($path)
                    ->as($filename)
                    ->withMime('application/pdf');
            }

            if ($attachments !== []) {
                return $attachments;
            }
        }

        if (isset($this->mailData['pdf']) && method_exists($this->mailData['pdf'], 'output')) {
            $attachments[] = Attachment::fromData(
                fn () => $this->mailData['pdf']->output(),
                'Loyalty-Upgrade-Scheme-Policy.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
