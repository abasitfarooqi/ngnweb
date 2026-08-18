<?php

namespace App\Mail\Concerns;

use App\Services\Communications\CommunicationOutboundCoordinator;
use Throwable;

trait UsesTransactionalCommunicationPolicy
{
    public function send($mailer)
    {
        $coordinator = app(CommunicationOutboundCoordinator::class);
        $plan = $coordinator->begin($this);

        if (! $plan->sendEmail) {
            $coordinator->finish($this, $plan, emailSent: false);

            return null;
        }

        try {
            $result = parent::send($mailer);
            $coordinator->finish($this, $plan, emailSent: true, sentMessage: $result);

            return $result;
        } catch (Throwable $exception) {
            $coordinator->finish(
                $this,
                $plan,
                emailSent: false,
                failureReason: $exception->getMessage(),
            );

            throw $exception;
        }
    }
}
