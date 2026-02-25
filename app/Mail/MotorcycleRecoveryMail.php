<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorcycleRecoveryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $distance;

    public $fromAddress;

    public $toAddress;

    public $userDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(float $distance, string $fromAddress, string $toAddress, array $userDetails)
    {
        $this->distance = $distance;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->userDetails = $userDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Motorcycle Recovery Request - NGN')
            ->view('emails.motorcycle_recovery')
            ->priority(1); // High priority for recovery requests
    }
}
