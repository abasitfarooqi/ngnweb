<?php

namespace App\Mail;

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
            ->view($this->template)
            ->with(['subscriber' => $this->subscriber], ['used_motorbike' => $this->used_motorbike]);
    }
}
