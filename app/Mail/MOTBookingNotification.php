<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MOTBookingNotification extends Mailable
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
            subject: 'MOT Appointment Confirmation',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.mot_booking_confirmation',
            with: $this->mailData
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->view('emails.mot_booking_confirmation')
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
                'address' => $this->mailData['address'],
                'payment_method' => $this->mailData['payment_method'],
                'payment_notes' => $this->mailData['payment_notes'],
                'is_paid' => $this->mailData['is_paid'],
            ]);
    }
}
