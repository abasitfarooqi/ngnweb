<?php

namespace App\Services\Communications;

use App\Models\CommunicationDefinition;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionalEmailPolicy
{
    public function __construct(
        private readonly CommunicationSystemSwitch $switch,
        private readonly CommunicationSchema $schema,
    ) {}

    public function shouldSendMailable(Mailable $mailable): bool
    {
        return $this->decisionForMailable($mailable)->sendEmail;
    }

    public function decisionForMailable(Mailable $mailable): CommunicationSendDecision
    {
        if (! $this->switch->enabled() || ! $this->schema->ready()) {
            return new CommunicationSendDecision(
                legacy: true,
                sendEmail: true,
                recordSnapshot: false,
                createInbox: false,
                definition: null,
            );
        }

        if ($this->hasOnlyInternalRecipients($mailable)) {
            return new CommunicationSendDecision(
                legacy: false,
                sendEmail: true,
                recordSnapshot: false,
                createInbox: false,
                definition: null,
            );
        }

        try {
            $definition = CommunicationDefinition::query()
                ->with('policy')
                ->where('classification', 'transactional')
                ->where('email_class', $mailable::class)
                ->first();

            if (! $definition || ! $definition->active) {
                return new CommunicationSendDecision(
                    legacy: true,
                    sendEmail: true,
                    recordSnapshot: false,
                    createInbox: false,
                    definition: $definition,
                );
            }

            $emailEnabled = (bool) ($definition->policy?->email_enabled ?? true);
            $inboxEnabled = (bool) ($definition->policy?->internal_inbox_enabled ?? false);
            $webPushEnabled = (bool) ($definition->policy?->web_push_enabled ?? false);
            $mobilePushEnabled = (bool) ($definition->policy?->mobile_push_enabled ?? false);

            return new CommunicationSendDecision(
                legacy: false,
                sendEmail: $emailEnabled,
                recordSnapshot: $emailEnabled || $inboxEnabled || $webPushEnabled || $mobilePushEnabled,
                createInbox: $inboxEnabled,
                definition: $definition,
            );
        } catch (Throwable $exception) {
            Log::warning('Transactional email policy lookup failed; preserving legacy email delivery.', [
                'mailable' => $mailable::class,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return new CommunicationSendDecision(
                legacy: true,
                sendEmail: true,
                recordSnapshot: false,
                createInbox: false,
                definition: null,
            );
        }
    }

    /**
     * @param  string|array<int, string>|null  $recipients
     */
    public function shouldSendKey(string $key, string|array|null $recipients = null): bool
    {
        if (! $this->switch->enabled() || ! $this->schema->ready()) {
            return true;
        }

        if ($this->addressesAreInternalOnly((array) $recipients)) {
            return true;
        }

        try {
            $definition = CommunicationDefinition::query()
                ->with('policy')
                ->where('classification', 'transactional')
                ->where('key', $key)
                ->first();

            if (! $definition) {
                return true;
            }

            if (! $definition->active) {
                return true;
            }

            return (bool) ($definition->policy?->email_enabled ?? true);
        } catch (Throwable $exception) {
            Log::warning('Transactional email key policy lookup failed; preserving legacy email delivery.', [
                'key' => $key,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return true;
        }
    }

    private function hasOnlyInternalRecipients(Mailable $mailable): bool
    {
        $addresses = $this->recipientAddresses($mailable);

        if ($addresses === []) {
            return false;
        }

        return $this->addressesAreInternalOnly($addresses);
    }

    /**
     * @return list<string>
     */
    private function recipientAddresses(Mailable $mailable): array
    {
        $recipients = [];

        foreach (['to', 'cc', 'bcc'] as $property) {
            foreach ((array) ($mailable->{$property} ?? []) as $recipient) {
                if (is_array($recipient) && isset($recipient['address'])) {
                    $recipients[] = strtolower((string) $recipient['address']);
                } elseif (is_string($recipient)) {
                    $recipients[] = strtolower($recipient);
                }
            }
        }

        return array_values(array_filter(array_unique($recipients)));
    }

    public function isCustomerAddress(string $email): bool
    {
        return ! $this->isInternalAddress($email);
    }

    private function isInternalAddress(string $email): bool
    {
        $email = strtolower(trim($email));
        $domain = str_contains($email, '@') ? substr(strrchr($email, '@') ?: '', 1) : '';

        if (in_array($email, array_map('strtolower', (array) config('communications.internal_email_addresses', [])), true)) {
            return true;
        }

        return $domain !== ''
            && in_array($domain, array_map('strtolower', (array) config('communications.internal_email_domains', [])), true);
    }

    /**
     * @param  array<int, mixed>  $addresses
     */
    private function addressesAreInternalOnly(array $addresses): bool
    {
        $addresses = array_values(array_filter(array_map(
            fn (mixed $address): string => strtolower(trim((string) $address)),
            $addresses
        )));

        if ($addresses === []) {
            return false;
        }

        foreach ($addresses as $address) {
            if (! $this->isInternalAddress($address)) {
                return false;
            }
        }

        return true;
    }
}
