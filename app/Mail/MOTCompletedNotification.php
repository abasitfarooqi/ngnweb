<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MOTCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MOT Completed',
        );
    }

    public function content()
    {
        return new Content(
            view: 'olders.emails.mot_completed',
            with: $this->mailData
        );
    }

    public function build()
    {
        return $this->view('olders.emails.mot_completed')
            ->with([
                'customer_name' => $this->mailData['customer_name'],
                'vehicle_registration' => $this->mailData['vehicle_registration'],
                'vehicle_chassis' => $this->mailData['vehicle_chassis'],
                'vehicle_color' => $this->mailData['vehicle_color'],
                'payment_link' => $this->mailData['payment_link'],
                'date_of_appointment' => $this->mailData['date_of_appointment'],
                'start' => $this->mailData['start'],
                'end' => $this->mailData['end'],
                'notes' => $this->mailData['notes'],
                'payment_method' => $this->mailData['payment_method'],
                'payment_notes' => $this->mailData['payment_notes'],
                'is_paid' => $this->mailData['is_paid'],

            ]);
    }
}
