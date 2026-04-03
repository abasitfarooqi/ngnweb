<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CronJobReportMailer extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        Log::info('CronJobReportMailer constructed with data:', $this->data);
    }

    public function build(): self
    {
        $title = 'Global Stock Update - '.$this->data['title'];

        return $this->subject($title)
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.cron-jobs.cron-job-global-stock-report',
                [
                    'total_products' => $this->data['data']['total_products'],
                    'positive_stock' => $this->data['data']['positive_stock'],
                    'zero_stock' => $this->data['data']['zero_stock'],
                    'negative_stock' => $this->data['data']['negative_stock'],
                    'total_stock' => $this->data['data']['total_stock'],
                ],
                $title,
            ));
    }
}
