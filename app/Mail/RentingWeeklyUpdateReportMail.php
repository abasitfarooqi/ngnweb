<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RentingWeeklyUpdateReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        public array $report,
        public string $pdfPath,
    ) {}

    public function envelope(): Envelope
    {
        $start = $this->report['start']->format('d M Y');
        $end = $this->report['end']->format('d M Y');

        return new Envelope(subject: 'Weekly rental chase report '.$start.' – '.$end);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.agreement-controller-universal',
            with: UniversalMailPayload::wrap(
                'emails.renting-weekly-update-report',
                ['report' => $this->report],
                'Weekly rental chase report',
            ),
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists($this->pdfPath)) {
            return [];
        }

        $filename = 'rental-weekly-follow-up-'.$this->report['start']->format('Ymd').'-'.$this->report['end']->format('Ymd').'.pdf';

        return [
            Attachment::fromData(fn () => $disk->get($this->pdfPath), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
