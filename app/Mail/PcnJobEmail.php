<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PcnJobEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $template;

    public function __construct($data, $template)
    {
        $this->data = $data;
        $this->template = $template;
    }

    public function build()
    {
        // $t1 = 'emails.pcn.t1';
        // $t2 = 'emails.pcn.t2';
        // $t3 = 'emails.pcn.t3';
        // $t4 = 'emails.pcn.t4';
        // $t5 = 'emails.pcn.t5';
        // $t6 = 'emails.pcn.t6';
        // $t7 = 'emails.pcn.t7';

        $t_template = 'emails.pcn.'.$this->template;
        $subject = 'Action Required...! Unsettled PCNs';

        return $this->subject($subject)
            ->view('emails.templates.agreement-controller-universal')
            ->with([
                'mailData' => UniversalMailPayload::fromLegacyEmailView(
                    $t_template,
                    ['data' => $this->data],
                    ['title' => $subject],
                ),
            ]);
    }
}
