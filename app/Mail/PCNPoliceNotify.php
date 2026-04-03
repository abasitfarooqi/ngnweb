<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PCNPoliceNotify extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        \Log::info('PCNNotify:__construct', ['data' => $data]);
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Payment Reminder for PCN')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.pcn-notify-police',
                ['data' => $this->data],
                'Payment Reminder for PCN',
            ));
    }
}
