<?php

namespace App\Mail;

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
        return $this->view('emails.cron-jobs.cron-job-global-stock-report')
            ->subject('Global Stock Update - '.$this->data['title'])
            ->with([
                'total_products' => $this->data['data']['total_products'],
                'positive_stock' => $this->data['data']['positive_stock'],
                'zero_stock' => $this->data['data']['zero_stock'],
                'negative_stock' => $this->data['data']['negative_stock'],
                'total_stock' => $this->data['data']['total_stock'],
            ]);
    }
}
