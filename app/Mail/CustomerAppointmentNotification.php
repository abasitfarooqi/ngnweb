<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerAppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Customer Appointment Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'olders.emails.servicesandbooking.customer_appointment',
            with: $this->data
        );
    }

    public function build()
    {
        return $this->view('olders.emails.servicesandbooking.customer_appointment')
            ->with([
                'appointment_date' => $this->data['appointment_date'],
                'customer_name' => $this->data['customer_name'],
                'registration_number' => $this->data['registration_number'],
                'contact_number' => $this->data['contact_number'],
                'is_resolved' => $this->data['is_resolved'],
                'email' => $this->data['email'],
                'booking_reason' => $this->data['booking_reason'],
            ]);
    }
}
