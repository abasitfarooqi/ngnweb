<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MOTTaxNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;

    public $template;

    public $subjectLine;

    public $used_motorbike;

    public function __construct($subscriber, $template, $subjectLine, $used_motorbike)
    {
        $this->subscriber = $subscriber;
        $this->template = $template;
        $this->subjectLine = $subjectLine;
        $this->used_motorbike = $used_motorbike;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.templates.agreement-controller-universal')
            ->with([
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    $this->template,
                    [
                        'subscriber' => $this->subscriber,
                        'used_motorbike' => $this->used_motorbike,
                    ],
                    ['title' => $this->subjectLine],
                ),
            ]);
    }
}
