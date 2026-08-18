<?php

namespace App\Services\Communications;

use Illuminate\Mail\Mailable;
use Throwable;

class CommunicationOutboundCoordinator
{
    public function __construct(
        private readonly TransactionalEmailPolicy $policy,
        private readonly CommunicationSnapshotWriter $writer,
        private readonly CommunicationAttachmentCopier $attachments,
        private readonly CommunicationPushDispatcher $push,
    ) {}

    public function begin(Mailable $mailable): CommunicationOutboundPlan
    {
        $decision = $this->policy->decisionForMailable($mailable);
        $customerEmail = $this->writer->customerEmail($mailable);
        $customerAuth = $customerEmail !== null
            ? $this->writer->customerAuthForEmail($customerEmail)
            : null;

        $sendEmail = $decision->sendEmail;
        $legacyEmailFallback = false;

        if ($decision->recordSnapshot && $customerEmail !== null && $customerAuth === null) {
            $sendEmail = true;
            $legacyEmailFallback = ! $decision->sendEmail;
        }

        $plan = new CommunicationOutboundPlan(
            decision: $decision,
            sendEmail: $sendEmail,
            legacyEmailFallback: $legacyEmailFallback,
            customerEmail: $customerEmail,
            customerAuth: $customerAuth,
        );

        if (! $decision->recordSnapshot || $decision->definition === null) {
            return $plan;
        }

        $plan->communication = $this->writer->begin($mailable, $plan);

        if ($plan->communication !== null && $plan->sendEmail) {
            $uuid = $plan->communication->uuid;
            $mailable->withSymfonyMessage(function ($message) use ($uuid): void {
                $message->getHeaders()->addTextHeader('X-NGN-Communication-UUID', $uuid);
            });
        }

        return $plan;
    }

    public function finish(
        Mailable $mailable,
        CommunicationOutboundPlan $plan,
        bool $emailSent,
        ?string $failureReason = null,
        mixed $sentMessage = null,
    ): void {
        if ($plan->communication === null) {
            return;
        }

        try {
            $this->writer->finishEmail(
                $plan->communication,
                $emailSent,
                $failureReason,
                $plan->legacyEmailFallback,
                $this->messageIdFromSent($sentMessage),
            );
            $this->attachments->copyFromMailable($mailable, $plan->communication);
            $this->push->dispatch($plan->communication->fresh(['deliveries']) ?? $plan->communication, $plan);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function messageIdFromSent(mixed $sentMessage): ?string
    {
        if (! is_object($sentMessage) || ! method_exists($sentMessage, 'getMessageId')) {
            return null;
        }

        $messageId = trim((string) $sentMessage->getMessageId(), " \t\n\r\0\x0B<>");

        return $messageId !== '' ? $messageId : null;
    }
}
