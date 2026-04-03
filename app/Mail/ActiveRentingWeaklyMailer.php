<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ActiveRentingWeaklyMailer extends Mailable
{
    use Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        Log::info('Active Renting Weakly Mailer constructed with data: '.json_encode($this->data));
    }

    public function build(): self
    {
        return $this->subject('Active Renting Weekly Report')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.cron-jobs.active_renting_weakly_mailer',
                [
                    'active_bookings' => $this->data['active_bookings'],
                    'stats' => $this->data['stats'] ?? null,
                ],
                'Active Renting Weekly Report',
            ));
    }

    // public function attachments(): array
    // {
    //     return [
    //         Attachment::fromStorage('reports/active_rentals_report.pdf')
    //             ->as('active_renting_weakly_report.pdf')
    //             ->withMime('application/pdf'),
    //     ];
    // }
}
