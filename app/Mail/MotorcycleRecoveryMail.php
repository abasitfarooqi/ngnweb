<?php

namespace App\Mail;

use App\Support\UniversalMailPayload;
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
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                'livewire.agreements.migrated.emails.motorcycle_recovery',
                [
                    'distance' => $this->distance,
                    'fromAddress' => $this->fromAddress,
                    'toAddress' => $this->toAddress,
                    'userDetails' => $this->userDetails,
                ],
                'Motorcycle Recovery Request - NGN',
            ))
            ->priority(1);
    }
}
