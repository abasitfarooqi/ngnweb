<?php

namespace App\Mail\Concerns;

use App\Services\Communications\CommunicationSnapshotWriter;
use App\Services\Communications\TransactionalEmailPolicy;
use Throwable;

trait UsesTransactionalCommunicationPolicy
{
    public function send($mailer)
    {
        $decision = app(TransactionalEmailPolicy::class)->decisionForMailable($this);
        $writer = app(CommunicationSnapshotWriter::class);

        if (! $decision->sendEmail) {
            $writer->recordFromMailable($this, $decision, emailSent: false);

            return null;
        }

        try {
            $result = parent::send($mailer);
            $writer->recordFromMailable($this, $decision, emailSent: true);

            return $result;
        } catch (Throwable $exception) {
            $writer->recordFromMailable(
                $this,
                $decision,
                emailSent: false,
                failureReason: $exception->getMessage(),
            );

            throw $exception;
        }
    }
}
