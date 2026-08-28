<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\CommunicationRecipient;
use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Support\Communications\CommunicationPreviewText;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CommunicationSnapshotWriter
{
    public function begin(Mailable $mailable, CommunicationOutboundPlan $plan): ?Communication
    {
        if (! $plan->decision->recordSnapshot || $plan->decision->definition === null) {
            return null;
        }

        try {
            return $this->write($mailable, $plan);
        } catch (Throwable $exception) {
            Log::warning('Transactional communication snapshot was not stored; email path was left unchanged.', [
                'mailable' => $mailable::class,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function finishEmail(
        Communication $communication,
        bool $emailSent,
        ?string $failureReason = null,
        bool $legacyEmailFallback = false,
        ?string $providerMessageId = null,
    ): void {
        $delivery = $communication->deliveries()
            ->where('channel', 'email')
            ->latest('id')
            ->first();

        if ($delivery === null) {
            return;
        }

        $now = now();
        $status = $delivery->status;

        if ($emailSent) {
            if (! in_array($status, ['delivered', 'opened'], true)) {
                $status = 'sent';
            }
        } elseif ($failureReason) {
            $status = 'failed';
        } else {
            $status = 'skipped';
        }

        $metadata = is_array($delivery->metadata) ? $delivery->metadata : [];
        $metadata['email_sent'] = $emailSent;
        $metadata['legacy_email_fallback'] = $legacyEmailFallback;

        $delivery->forceFill([
            'status' => $status,
            'provider' => $delivery->provider ?: config('mail.default'),
            'provider_message_id' => $providerMessageId ?: $delivery->provider_message_id,
            'sent_at' => $emailSent ? ($delivery->sent_at ?: $now) : $delivery->sent_at,
            'failed_at' => $failureReason ? $now : $delivery->failed_at,
            'failure_reason' => $failureReason,
            'metadata' => $metadata,
        ])->save();
    }

    public function customerEmail(Mailable $mailable): ?string
    {
        $policy = app(TransactionalEmailPolicy::class);

        foreach (['to', 'cc', 'bcc'] as $property) {
            foreach ((array) ($mailable->{$property} ?? []) as $recipient) {
                $address = is_array($recipient)
                    ? strtolower(trim((string) ($recipient['address'] ?? '')))
                    : strtolower(trim((string) $recipient));

                if ($address === '' || ! str_contains($address, '@')) {
                    continue;
                }

                if ($policy->isCustomerAddress($address)) {
                    return $address;
                }
            }
        }

        return null;
    }

    public function customerAuthForEmail(string $email): ?CustomerAuth
    {
        $email = strtolower(trim($email));

        $auth = CustomerAuth::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($auth) {
            return $auth;
        }

        $customer = Customer::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($customer === null) {
            return null;
        }

        return CustomerAuth::query()
            ->where('customer_id', $customer->id)
            ->first();
    }

    private function write(Mailable $mailable, CommunicationOutboundPlan $plan): Communication
    {
        $decision = $plan->decision;
        $definition = $decision->definition;
        $policy = $definition?->policy;
        $customerAuth = $plan->customerAuth;
        $customerEmail = $plan->customerEmail;
        $html = $this->renderedHtml($mailable);
        $text = CommunicationPreviewText::fromHtml($html, 0);
        $subject = $this->subject($mailable, $definition?->name ?? 'Customer notification');
        $preview = CommunicationPreviewText::readable($text, $subject);
        $now = now();
        $webPush = (bool) ($policy?->web_push_enabled ?? false);
        $mobilePush = (bool) ($policy?->mobile_push_enabled ?? false);
        $createRecipient = $customerAuth !== null && ($decision->createInbox || $webPush || $mobilePush);

        $communication = Communication::query()->create([
            'uuid' => (string) Str::uuid(),
            'communication_definition_id' => $definition?->id,
            'communication_key' => $definition?->key ?? $mailable::class,
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'recipient_email' => $customerEmail,
            'subject' => $subject,
            'title' => $definition?->name ?? $subject,
            'preview' => $preview !== '' ? $preview : $subject,
            'content_html' => $html,
            'content_text' => $text,
            'structured_content' => [
                'mailable' => $mailable::class,
                'recipient_email' => $customerEmail,
            ],
            'payload_snapshot' => [
                'mailable' => $mailable::class,
                'recipient_email' => $customerEmail,
                'legacy_email_fallback' => $plan->legacyEmailFallback,
            ],
            'policy_snapshot' => [
                'email_enabled' => (bool) ($policy?->email_enabled ?? true),
                'internal_inbox_enabled' => (bool) ($policy?->internal_inbox_enabled ?? false),
                'staff_copy_enabled' => (bool) ($policy?->staff_copy_enabled ?? false),
                'web_push_enabled' => $webPush,
                'mobile_push_enabled' => $mobilePush,
                'reply_allowed' => (bool) ($policy?->reply_allowed ?? false),
                'enquiry_allowed' => (bool) ($policy?->enquiry_allowed ?? false),
                'priority' => $policy?->priority ?? $definition?->priority,
            ],
            'source_type' => $mailable::class,
            'correlation_id' => (string) Str::uuid(),
            'priority' => $policy?->priority ?? $definition?->priority ?? 'normal',
            'category' => $definition?->category ?? 'general',
        ]);

        if ($createRecipient) {
            CommunicationRecipient::query()->create([
                'communication_id' => $communication->id,
                'customer_auth_id' => $customerAuth->id,
            ]);
        }

        CommunicationDelivery::query()->create([
            'communication_id' => $communication->id,
            'channel' => 'email',
            'status' => $plan->sendEmail ? 'pending' : 'skipped',
            'provider' => config('mail.default'),
            'queued_at' => $now,
            'metadata' => [
                'legacy_email_fallback' => $plan->legacyEmailFallback,
            ],
        ]);

        if ($decision->createInbox) {
            CommunicationDelivery::query()->create([
                'communication_id' => $communication->id,
                'channel' => 'internal_inbox',
                'status' => $customerAuth !== null ? 'delivered' : 'deferred',
                'queued_at' => $now,
                'sent_at' => $now,
                'delivered_at' => $customerAuth !== null ? $now : null,
                'failure_reason' => $customerAuth === null
                    ? 'Waiting for a customer portal account for this email address.'
                    : null,
            ]);
        }

        return $communication;
    }

    private function renderedHtml(Mailable $mailable): string
    {
        try {
            return (string) $mailable->render();
        } catch (Throwable) {
            return '';
        }
    }

    private function subject(Mailable $mailable, string $fallback): string
    {
        try {
            $envelope = $mailable->envelope();
            $subject = trim((string) ($envelope->subject ?? ''));

            return $subject !== '' ? $subject : $fallback;
        } catch (Throwable) {
            return $fallback;
        }
    }
}
